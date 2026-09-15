<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Package;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerLifecycleSeeder extends Seeder
{
    /**
     * Run the database seeds for real customer lifecycle states (Active, Suspended, Inactive).
     */
    public function run(): void
    {
        $package1 = Package::where('name', 'Cloud VPS 4')->first() ?? Package::first();
        $package2 = Package::where('name', 'Cloud VPS 6')->first() ?? Package::first();

        // 1. Suspended Customer: Marcus Vance (Has suspended service, 0 active)
        $suspendedUser = User::firstOrCreate(
            ['email' => 'marcus.vance@example.com'],
            [
                'name' => 'Marcus Vance',
                'phone' => '+1 (415) 890-1234',
                'company_name' => 'Vance Hosting LLC',
                'address' => '742 Market Street, Suite 400',
                'city' => 'San Francisco',
                'state' => 'California',
                'country' => 'United States',
                'zip_code' => '94103',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        if ($suspendedUser->services()->count() === 0) {
            $order = Order::create([
                'user_id' => $suspendedUser->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'total_amount' => $package1->price_monthly ?? 6.60,
                'status' => 'completed',
            ]);

            Service::create([
                'user_id' => $suspendedUser->id,
                'order_id' => $order->id,
                'package_id' => $package1->id,
                'server_name' => 'vps-us-vance.vortexcloud.net',
                'ip_address' => '194.163.170.82',
                'status' => 'suspended',
                'billing_cycle' => 'monthly',
                'recurring_amount' => $package1->price_monthly ?? 6.60,
                'next_due_date' => now()->subDays(5),
                'region' => 'US-central',
                'specs_snapshot' => [
                    'cores' => '4 vCPU Cores',
                    'memory' => '6 GB RAM',
                    'storage' => '100 GB NVMe',
                    'datacenter' => 'US-Central (St. Louis)',
                ],
            ]);
        }

        // 2. Inactive Customer: Sophia Chen (Cancelled order, 0 live/suspended services)
        $inactiveUser = User::firstOrCreate(
            ['email' => 'sophia.chen@example.com'],
            [
                'name' => 'Sophia Chen',
                'phone' => '+1 (604) 239-8871',
                'company_name' => 'Chen Creative Studio',
                'address' => '1200 Burrard Street',
                'city' => 'Vancouver',
                'state' => 'British Columbia',
                'country' => 'Canada',
                'zip_code' => 'V6Z 2C7',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        if ($inactiveUser->orders()->count() === 0) {
            Order::create([
                'user_id' => $inactiveUser->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'total_amount' => $package2->price_monthly ?? 9.00,
                'status' => 'cancelled',
            ]);
        }

        // 3. Active Customer: Elena Rostova (1 active server)
        $activeUser = User::firstOrCreate(
            ['email' => 'elena.rostova@example.com'],
            [
                'name' => 'Elena Rostova',
                'phone' => '+49 30 1234567',
                'company_name' => 'Nordic Cloud GmbH',
                'address' => 'Friedrichstraße 43',
                'city' => 'Berlin',
                'state' => 'Berlin',
                'country' => 'Germany',
                'zip_code' => '10117',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        if ($activeUser->services()->count() === 0) {
            $order = Order::create([
                'user_id' => $activeUser->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'total_amount' => $package2->price_monthly ?? 9.00,
                'status' => 'completed',
            ]);

            Service::create([
                'user_id' => $activeUser->id,
                'order_id' => $order->id,
                'package_id' => $package2->id,
                'server_name' => 'vps-eu-rostova.vortexcloud.net',
                'ip_address' => '161.97.142.55',
                'status' => 'active',
                'billing_cycle' => 'monthly',
                'recurring_amount' => $package2->price_monthly ?? 9.00,
                'next_due_date' => now()->addDays(25),
                'region' => 'EU',
                'specs_snapshot' => [
                    'cores' => '6 vCPU Cores',
                    'memory' => '16 GB RAM',
                    'storage' => '200 GB NVMe',
                    'datacenter' => 'EU (Frankfurt)',
                ],
            ]);
        }
    }
}
