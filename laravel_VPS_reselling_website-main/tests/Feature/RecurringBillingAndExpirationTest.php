<?php

namespace Tests\Feature;

use App\Mail\RenewalInvoiceMail;
use App\Mail\RenewalReminderMail;
use App\Mail\ServerTerminatedMail;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Package;
use App\Models\ProvisioningLog;
use App\Models\Service;
use App\Models\User;
use App\Services\Provisioning\MockProvisioningService;
use App\Services\Provisioning\ProvisioningServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RecurringBillingAndExpirationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Bind MockProvisioningService for testing
        $this->app->bind(ProvisioningServiceInterface::class, MockProvisioningService::class);
    }

    private function createTestUserAndService(array $serviceOverrides = []): array
    {
        $user = User::factory()->create([
            'name' => 'Alice Reseller Customer',
            'email' => 'alice@example.com',
        ]);

        $package = Package::create([
            'name' => 'Cloud VPS 8',
            'category' => 'vps',
            'specs' => ['cores' => '6', 'memory' => '16 GB', 'storage' => '200 GB SSD'],
            'price_monthly' => 12.50,
            'is_active' => true,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-TEST-999',
            'total_amount' => 12.50,
            'status' => 'completed',
        ]);

        $service = Service::create(array_merge([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'package_id' => $package->id,
            'contabo_instance_id' => 'mock_vps_99999',
            'server_name' => 'vps-test.vortexcloud.net',
            'ip_address' => '192.0.2.100',
            'status' => 'active',
            'billing_cycle' => 'monthly',
            'recurring_amount' => 12.50,
            'next_due_date' => now()->addDays(14)->toDateString(),
        ], $serviceOverrides));

        return [$user, $package, $order, $service];
    }

    public function test_process_renewals_generates_invoice_at_t_minus_14_days(): void
    {
        Mail::fake();

        [$user, $package, $order, $service] = $this->createTestUserAndService([
            'next_due_date' => now()->addDays(14)->toDateString(),
        ]);

        $this->artisan('billing:process-renewals')
            ->assertSuccessful();

        // Verify renewal invoice was generated in database
        $invoice = Invoice::where('service_id', $service->id)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals($user->id, $invoice->user_id);
        $this->assertEquals('unpaid', $invoice->status);
        $this->assertEquals(12.50, (float) $invoice->total);
        $this->assertEquals($service->next_due_date->toDateString(), $invoice->due_date->toDateString());
        $this->assertArrayHasKey('14', $invoice->dunning_reminders);

        // Verify RenewalInvoiceMail was dispatched
        Mail::assertSent(RenewalInvoiceMail::class, function ($mail) use ($user, $invoice) {
            return $mail->hasTo($user->email) && $mail->invoice->id === $invoice->id;
        });
    }

    public function test_process_renewals_does_not_duplicate_existing_invoice(): void
    {
        Mail::fake();

        [$user, $package, $order, $service] = $this->createTestUserAndService([
            'next_due_date' => now()->addDays(14)->toDateString(),
        ]);

        // First run creates invoice
        $this->artisan('billing:process-renewals')->assertSuccessful();
        $this->assertEquals(1, Invoice::where('service_id', $service->id)->count());

        // Second run must not create a duplicate invoice
        $this->artisan('billing:process-renewals')->assertSuccessful();
        $this->assertEquals(1, Invoice::where('service_id', $service->id)->count());
    }

    public function test_process_renewals_dispatches_dunning_reminders(): void
    {
        Mail::fake();

        // Test 7-day reminder
        [$user, $package, $order, $service] = $this->createTestUserAndService([
            'next_due_date' => now()->addDays(7)->toDateString(),
        ]);

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'service_id' => $service->id,
            'amount' => 12.50,
            'tax' => 0.00,
            'total' => 12.50,
            'status' => 'unpaid',
            'due_date' => $service->next_due_date,
            'dunning_reminders' => ['14' => now()->subDays(7)->toIso8601String()],
        ]);

        $this->artisan('billing:process-renewals')->assertSuccessful();

        $invoice->refresh();
        $this->assertArrayHasKey('7', $invoice->dunning_reminders);
        Mail::assertSent(RenewalReminderMail::class, function ($mail) {
            return $mail->daysRemaining === 7;
        });

        // Test 3-day reminder
        $service->update(['next_due_date' => now()->addDays(3)->toDateString()]);
        $invoice->update(['due_date' => $service->next_due_date]);

        $this->artisan('billing:process-renewals')->assertSuccessful();
        $invoice->refresh();
        $this->assertArrayHasKey('3', $invoice->dunning_reminders);

        // Test 0-day (due date) final warning
        $service->update(['next_due_date' => now()->toDateString()]);
        $invoice->update(['due_date' => $service->next_due_date]);

        $this->artisan('billing:process-renewals')->assertSuccessful();
        $invoice->refresh();
        $this->assertArrayHasKey('0', $invoice->dunning_reminders);
    }

    public function test_enforce_expirations_terminates_overdue_services_and_cancels_upstream(): void
    {
        Mail::fake();

        [$user, $package, $order, $service] = $this->createTestUserAndService([
            'next_due_date' => now()->subDay()->toDateString(), // Due yesterday
            'status' => 'active',
        ]);

        $unpaidInvoice = Invoice::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'service_id' => $service->id,
            'amount' => 12.50,
            'tax' => 0.00,
            'total' => 12.50,
            'status' => 'unpaid',
            'due_date' => $service->next_due_date,
        ]);

        $this->artisan('services:enforce-expirations')->assertSuccessful();

        $service->refresh();
        $unpaidInvoice->refresh();

        // 1. Service must be terminated
        $this->assertEquals('terminated', $service->status);

        // 2. Unpaid invoice must be cancelled
        $this->assertEquals('cancelled', $unpaidInvoice->status);

        // 3. ProvisioningLog must record upstream cancellation
        $log = ProvisioningLog::where('service_id', $service->id)
            ->where('action', 'terminate_due_to_non_renewal')
            ->first();
        $this->assertNotNull($log);
        $this->assertTrue($log->is_success);

        // 4. ServerTerminatedMail must be sent to user
        Mail::assertSent(ServerTerminatedMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_enforce_expirations_does_not_terminate_services_with_paid_renewals(): void
    {
        Mail::fake();

        [$user, $package, $order, $service] = $this->createTestUserAndService([
            'next_due_date' => now()->subDay()->toDateString(),
            'status' => 'active',
        ]);

        Invoice::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'service_id' => $service->id,
            'amount' => 12.50,
            'tax' => 0.00,
            'total' => 12.50,
            'status' => 'paid',
            'due_date' => $service->next_due_date,
            'paid_at' => now(),
        ]);

        $this->artisan('services:enforce-expirations')->assertSuccessful();

        $service->refresh();
        $this->assertEquals('active', $service->status);
        Mail::assertNotSent(ServerTerminatedMail::class);
    }

    public function test_paying_renewal_invoice_extends_service_billing_cycle(): void
    {
        [$user, $package, $order, $service] = $this->createTestUserAndService([
            'billing_cycle' => 'monthly',
            'next_due_date' => now()->addDays(5)->toDateString(),
            'status' => 'active',
        ]);

        $initialDue = $service->next_due_date->copy();

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'service_id' => $service->id,
            'amount' => 12.50,
            'tax' => 0.00,
            'total' => 12.50,
            'status' => 'unpaid',
            'due_date' => $service->next_due_date,
        ]);

        // Simulating invoice payment
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $service->refresh();

        // The next_due_date must have been extended by 1 month
        $this->assertEquals('active', $service->status);
        $this->assertEquals(
            $initialDue->copy()->addMonth()->toDateString(),
            $service->next_due_date->toDateString()
        );
    }

    public function test_email_templates_render_and_display_required_policy_details(): void
    {
        [$user, $package, $order, $service] = $this->createTestUserAndService([
            'next_due_date' => now()->addDays(14)->toDateString(),
        ]);

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'service_id' => $service->id,
            'amount' => 12.50,
            'tax' => 0.00,
            'total' => 12.50,
            'status' => 'unpaid',
            'due_date' => $service->next_due_date,
        ]);

        // 1. Renewal Invoice Mail
        $invoiceMail = new RenewalInvoiceMail($service, $invoice, $user);
        $invoiceMail->assertSeeInHtml($invoice->invoice_number);
        $invoiceMail->assertSeeInHtml('Cloud VPS 8');
        $invoiceMail->assertSeeInHtml('Strict Reseller Zero-Grace-Period Expiration Policy');

        // 2. Renewal Reminder Mail (Final Stage 0)
        $reminderMail = new RenewalReminderMail($service, $invoice, $user, 0);
        $reminderMail->assertSeeInHtml('FINAL WARNING');
        $reminderMail->assertSeeInHtml('Zero-Grace-Period Upstream Reseller Policy');

        // 3. Server Terminated Mail
        $terminatedMail = new ServerTerminatedMail($service, $user);
        $terminatedMail->assertSeeInHtml('Notice of Decommissioning Due to Non-Renewal');
        $terminatedMail->assertSeeInHtml('Why was this server terminated?');
    }
}
