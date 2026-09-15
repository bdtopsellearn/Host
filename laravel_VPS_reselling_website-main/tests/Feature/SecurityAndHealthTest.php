<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\Order;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class SecurityAndHealthTest extends TestCase
{
    use RefreshDatabase;

    public function test_healthz_endpoint_returns_healthy_status_with_services(): void
    {
        $response = $this->getJson('/healthz');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'healthy',
            'services' => [
                'database' => 'connected',
                'cache' => 'connected',
            ],
        ]);
        $response->assertJsonStructure(['status', 'timestamp', 'services', 'errors']);
    }

    public function test_security_headers_are_injected_on_responses(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    public function test_models_have_strict_mass_assignment_protection(): void
    {
        $this->assertFalse(
            \Illuminate\Database\Eloquent\Model::isUnguarded(),
            'Model::unguard() should NOT be globally active.'
        );

        $order = new Order();
        $this->assertEquals(
            ['user_id', 'order_number', 'total_amount', 'ip_address', 'status'],
            $order->getFillable()
        );
    }

    public function test_service_credentials_encrypt_and_decrypt_properly(): void
    {
        $user = User::factory()->create();
        $package = Package::create([
            'name' => 'Cloud VPS Test',
            'category' => 'vps',
            'price_monthly' => 15.00,
            'is_active' => true,
        ]);
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-TEST1234',
            'total_amount' => 15.00,
            'status' => 'completed',
        ]);

        $rawPassword = 'SuperSecretRootPass123!';
        $encryptedPassword = Crypt::encryptString($rawPassword);

        $service = Service::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'package_id' => $package->id,
            'status' => 'awaiting_provisioning',
            'billing_cycle' => 'monthly',
            'recurring_amount' => 15.00,
            'encrypted_credentials' => json_encode([
                'root_password' => $encryptedPassword,
                'hostname' => 'test-node.vortexcloud.net',
            ]),
        ]);

        $this->assertEquals($rawPassword, $service->decrypted_password);
    }

    public function test_demo_login_is_safeguarded_in_production_environment(): void
    {
        config(['app.demo_login_enabled' => true]);

        // Mock production environment
        $this->app->detectEnvironment(fn() => 'production');

        $adminLogin = new \App\Filament\Pages\Auth\Login();
        $response = $adminLogin->quickDemoLogin();

        $this->assertNull($response);
        $this->assertFalse(\Illuminate\Support\Facades\Auth::guard('admin')->check());
    }
}

