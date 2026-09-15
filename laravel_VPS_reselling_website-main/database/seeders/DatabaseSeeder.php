<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Generate Filament Shield Roles & Permissions automatically
        \Illuminate\Support\Facades\Artisan::call('shield:generate --all --panel=admin --no-interaction');

        // Create Default Customer
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Create Default Admin
        if (!\App\Models\Admin::where('email', 'admin@example.com')->exists()) {
            $admin = \App\Models\Admin::create([
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);

            $admin->assignRole('super_admin');
        }

        // Seed packages & addons & customer lifecycle states
        $this->call([
            PackageSeeder::class,
            PackageAddonSeeder::class,
            CustomerLifecycleSeeder::class,
        ]);
    }
}
