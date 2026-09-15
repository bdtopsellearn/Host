<?php

namespace Tests\Feature;

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_package_with_json_specs(): void
    {
        $package = Package::create([
            'name' => 'Cloud VPS 4',
            'description' => 'Test Description',
            'category' => 'vps',
            'specs' => [
                'cores' => '4',
                'memory' => '8 GB',
                'storage' => '100 GB SSD',
                'bandwidth' => '200 Mbit/s Port',
                'snapshots' => '1 Snapshot',
            ],
            'price_monthly' => 5.28,
            'price_quarterly' => 17.99,
            'setup_fee' => 7.99,
            'is_active' => true,
        ]);

        $this->assertNotNull($package->id);
        $this->assertEquals('Cloud VPS 4', $package->name);
        $this->assertIsArray($package->specs);
        $this->assertEquals('4', $package->specs['cores']);
        $this->assertEquals('8 GB', $package->specs['memory']);

        $fetched = Package::find($package->id);
        $this->assertIsArray($fetched->specs);
        $this->assertEquals('100 GB SSD', $fetched->specs['storage']);

        $package->delete();
    }
}
