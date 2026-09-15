<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Package;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clearResolvedInstances();
    }

    public function test_public_routes_have_rate_limiting_configured()
    {
        RateLimiter::clear('public:' . '127.0.0.1');

        $response = $this->get('/');
        $response->assertStatus(200);

        // Check header presence if headers are attached
        $this->assertNotNull(RateLimiter::limiter('public'));
    }

    public function test_configure_route_is_throttled_at_limit()
    {
        $package = Package::first();
        if (!$package) {
            $package = Package::create([
                'name' => 'Cloud VPS Test',
                'description' => 'Test',
                'category' => 'vps',
                'price_monthly' => 10,
                'is_active' => true,
            ]);
        }

        $ip = '192.168.1.50';
        RateLimiter::clear('configure:' . $ip);

        // Send 15 allowed requests
        for ($i = 0; $i < 15; $i++) {
            $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])
                ->post(route('checkout.configure', $package->id), [
                    'billing_cycle' => 'monthly',
                ]);
            $this->assertNotEquals(429, $response->getStatusCode(), "Request {$i} should not be throttled.");
        }

        // 16th request must be throttled with 429 for JSON
        $throttledResponse = $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->postJson(route('checkout.configure', $package->id), [
                'billing_cycle' => 'monthly',
            ]);

        $this->assertEquals(429, $throttledResponse->getStatusCode());
        $throttledResponse->assertJsonStructure(['error']);
    }

    public function test_payment_route_throttles_excessive_attempts()
    {
        $package = Package::first();
        if (!$package) {
            $package = Package::create([
                'name' => 'Cloud VPS Test',
                'description' => 'Test',
                'category' => 'vps',
                'price_monthly' => 10,
                'is_active' => true,
            ]);
        }

        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Rate Limit Test User',
                'email' => 'ratelimit@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $ip = '192.168.1.99';

        // Send 5 attempts
        for ($i = 0; $i < 5; $i++) {
            $response = $this->actingAs($user)
                ->withServerVariables(['REMOTE_ADDR' => $ip])
                ->post(route('checkout.process', $package->id), [
                    'payment_type' => 'manual',
                ]);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 6th attempt must be throttled with HTTP 429 for JSON API
        $throttled = $this->actingAs($user)
            ->withServerVariables(['REMOTE_ADDR' => $ip])
            ->postJson(route('checkout.process', $package->id), [
                'payment_type' => 'manual',
            ]);

        $this->assertEquals(429, $throttled->getStatusCode());
        $throttled->assertJsonStructure(['error']);
    }
}
