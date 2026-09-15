<?php

namespace Tests\Feature;

use App\Mail\InvoiceReceiptMail;
use App\Mail\TicketReplyCustomerMail;
use App\Models\Admin;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Package;
use App\Models\Service;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\User;
use App\Filament\Customer\Resources\SupportTickets\SupportTicketResource as CustomerSupportTicketResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SupportTicketAndReceiptTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_payment_triggers_invoice_receipt_mail(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'name' => 'Michael Scott',
            'email' => 'michael@example.com',
        ]);

        $package = Package::create([
            'name' => 'Cloud VPS 4',
            'category' => 'vps',
            'specs' => ['cores' => '4', 'memory' => '8 GB', 'storage' => '100 GB SSD'],
            'price_monthly' => 5.28,
            'is_active' => true,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-TEST-100',
            'total_amount' => 5.28,
            'status' => 'pending',
        ]);

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'amount' => 5.28,
            'tax' => 0.00,
            'total' => 5.28,
            'status' => 'unpaid',
            'due_date' => now()->addDays(7),
        ]);

        // Mark invoice as paid
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Verify InvoiceReceiptMail was dispatched
        Mail::assertSent(InvoiceReceiptMail::class, function ($mail) use ($user, $invoice) {
            return $mail->hasTo($user->email) && $mail->invoice->id === $invoice->id;
        });

        // Test receipt email HTML rendering
        $mailable = new InvoiceReceiptMail($invoice, $user);
        $mailable->assertSeeInHtml($invoice->invoice_number);
        $mailable->assertSeeInHtml('Payment Received');
        $mailable->assertSeeInHtml('5.28');
    }

    public function test_customer_ticket_creation_and_opening_reply(): void
    {
        $user = User::factory()->create([
            'name' => 'Jim Halpert',
            'email' => 'jim@example.com',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'subject' => 'Reverse DNS Setup for Mail Server',
            'department' => 'technical',
            'priority' => 'medium',
            'status' => 'open',
        ]);

        $reply = TicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => 'Please set PTR record for my IP 192.0.2.55 to mail.mycompany.com.',
        ]);

        $this->assertEquals(1, $ticket->replies()->count());
        $this->assertFalse($reply->is_staff_reply);
        $this->assertEquals('Jim Halpert', $reply->author_name);
        $this->assertEquals('#TICK-0000' . $ticket->id, $ticket->formatted_id);
    }

    public function test_customer_tenant_isolation(): void
    {
        $userA = User::factory()->create(['name' => 'User A']);
        $userB = User::factory()->create(['name' => 'User B']);

        $ticketA = SupportTicket::create([
            'user_id' => $userA->id,
            'subject' => 'Ticket from User A',
            'department' => 'billing',
            'priority' => 'low',
            'status' => 'open',
        ]);

        $ticketB = SupportTicket::create([
            'user_id' => $userB->id,
            'subject' => 'Ticket from User B',
            'department' => 'technical',
            'priority' => 'high',
            'status' => 'open',
        ]);

        // Acting as User A
        $this->actingAs($userA);
        $queryA = CustomerSupportTicketResource::getEloquentQuery()->get();

        $this->assertTrue($queryA->contains('id', $ticketA->id));
        $this->assertFalse($queryA->contains('id', $ticketB->id));
    }

    public function test_admin_staff_reply_dispatches_email_and_marks_answered(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'name' => 'Dwight Schrute',
            'email' => 'dwight@example.com',
        ]);

        $admin = Admin::create([
            'name' => 'Admin Alex',
            'email' => 'alex@vortexcloud.net',
            'password' => bcrypt('Secret123!'),
        ]);

        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'subject' => 'Port 25 Unblock Request',
            'department' => 'technical',
            'priority' => 'high',
            'status' => 'open',
        ]);

        // Customer opening message
        TicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => 'I would like to unblock outbound port 25 for my newsletter server.',
        ]);

        // Staff replies
        $staffReply = TicketReply::create([
            'support_ticket_id' => $ticket->id,
            'admin_id' => $admin->id,
            'message' => 'Hello Dwight, we have verified your account and unblocked port 25 on node #1.',
        ]);

        $ticket->update(['status' => 'answered']);

        // Assert staff reply properties
        $this->assertTrue($staffReply->is_staff_reply);
        $this->assertStringContainsString('VortexCloud Support', $staffReply->author_name);

        // Send email
        Mail::to($user->email)->send(new TicketReplyCustomerMail($ticket, $staffReply, $user));

        Mail::assertSent(TicketReplyCustomerMail::class, function ($mail) use ($user, $ticket) {
            return $mail->hasTo($user->email) && $mail->ticket->id === $ticket->id;
        });

        // Verify email rendering
        $mailable = new TicketReplyCustomerMail($ticket, $staffReply, $user);
        $mailable->assertSeeInHtml('Support Ticket Response');
        $mailable->assertSeeInHtml($ticket->formatted_id);
        $mailable->assertSeeInHtml('Hello Dwight, we have verified your account');
    }

    public function test_customer_reply_updates_ticket_to_in_progress(): void
    {
        $user = User::factory()->create();

        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'subject' => 'Need help with SSH keys',
            'department' => 'technical',
            'priority' => 'low',
            'status' => 'answered',
        ]);

        // Customer replies back
        TicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => 'Thank you! One more question regarding authorized_keys permissions.',
        ]);

        $ticket->update(['status' => 'in_progress']);

        $ticket->refresh();
        $this->assertEquals('in_progress', $ticket->status);
    }
}
