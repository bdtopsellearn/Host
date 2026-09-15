<?php

namespace Tests\Feature;

use App\Mail\ServiceDeliveredMail;
use App\Models\Order;
use App\Models\Package;
use App\Models\Service;
use App\Models\User;
use App\Services\Provisioning\MockProvisioningService;
use App\Services\Provisioning\ProvisioningServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContaboProvisioningTest extends TestCase
{
    use RefreshDatabase;

    public function test_provisioning_service_container_binding(): void
    {
        $service = app(ProvisioningServiceInterface::class);
        $this->assertInstanceOf(ProvisioningServiceInterface::class, $service);
    }

    public function test_mock_provisioning_service_creates_instance(): void
    {
        $mock = new MockProvisioningService();
        $result = $mock->createInstance([
            'product_id' => 'V153',
            'region' => 'EU',
            'default_user' => 'root',
        ]);

        $this->assertTrue($result->success);
        $this->assertNotEmpty($result->data['instanceId']);
        $this->assertNotEmpty($result->data['ipAddress']);
        $this->assertNotEmpty($result->data['initialPassword']);
        $this->assertEquals('root', $result->data['defaultUser']);
    }

    public function test_mock_provisioning_power_actions(): void
    {
        $mock = new MockProvisioningService();
        $instanceId = 'mock_123456';

        $this->assertTrue($mock->startInstance($instanceId)->success);
        $this->assertTrue($mock->stopInstance($instanceId)->success);
        $this->assertTrue($mock->rebootInstance($instanceId)->success);
        $this->assertTrue($mock->shutdownInstance($instanceId)->success);
        $this->assertTrue($mock->resetPassword($instanceId, 'NewP@ssw0rd123!')->success);
        $this->assertTrue($mock->rescueInstance($instanceId)->success);
        $this->assertTrue($mock->cancelInstance($instanceId)->success);
    }

    public function test_service_delivered_email_rendering(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'name' => 'John Customer',
            'email' => 'customer@example.com',
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
            'order_number' => 'ORD-TEST1234',
            'total_amount' => 5.28,
            'status' => 'pending_approval',
        ]);

        $service = Service::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'package_id' => $package->id,
            'contabo_instance_id' => '12345',
            'ip_address' => '194.163.140.42',
            'encrypted_credentials' => encrypt('SecretPass123!'),
            'default_user' => 'root',
            'os_image' => 'Ubuntu 22.04 LTS',
            'region' => 'EU',
            'status' => 'active',
            'billing_cycle' => 'monthly',
        ]);

        $mailable = new ServiceDeliveredMail($service, $user, 'SecretPass123!', 'root');
        $mailable->assertSeeInHtml('Cloud VPS 4');
        $mailable->assertSeeInHtml('194.163.140.42');
        $mailable->assertSeeInHtml('SecretPass123!');
        $mailable->assertSeeInHtml('root');

        Mail::to($user->email)->send($mailable);
        Mail::assertSent(ServiceDeliveredMail::class);
    }
}
