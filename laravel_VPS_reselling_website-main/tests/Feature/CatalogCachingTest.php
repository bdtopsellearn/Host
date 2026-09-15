<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Package;
use App\Models\PackageAddon;
use App\Services\AddonResolverService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class CatalogCachingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_packages_are_cached_and_auto_invalidated_on_update()
    {
        $package = Package::create([
            'name' => 'Cloud VPS Alpha',
            'description' => 'Test Desc',
            'category' => 'vps',
            'price_monthly' => 12.50,
            'is_active' => true,
        ]);

        $this->assertFalse(Cache::has('catalog:packages:active'));

        // First call caches the collection
        $cached = Package::getCachedActivePackages();
        $this->assertTrue(Cache::has('catalog:packages:active'));
        $this->assertCount(1, $cached);
        $this->assertEquals('Cloud VPS Alpha', $cached->first()->name);

        // Update package: booted hook must automatically purge the cache
        $package->update(['name' => 'Cloud VPS Beta']);
        $this->assertFalse(Cache::has('catalog:packages:active'), 'Cache should be invalidated on package update');

        // Next call fetches fresh data and re-caches
        $fresh = Package::getCachedActivePackages();
        $this->assertTrue(Cache::has('catalog:packages:active'));
        $this->assertEquals('Cloud VPS Beta', $fresh->first()->name);

        // Deleting package also invalidates
        $package->delete();
        $this->assertFalse(Cache::has('catalog:packages:active'), 'Cache should be invalidated on package deletion');
    }

    public function test_addons_are_cached_per_package_and_invalidated_on_addon_change()
    {
        $package = Package::create([
            'name' => 'Cloud VPS Delta',
            'description' => 'Test Delta',
            'category' => 'vps',
            'price_monthly' => 20.00,
            'is_active' => true,
        ]);

        $addon = PackageAddon::create([
            'package_id' => $package->id,
            'is_global' => false,
            'is_enabled' => true,
            'type' => 'os',
            'category' => 'os',
            'name' => 'Ubuntu 24.04',
            'value' => 'ubuntu_24_04',
            'price' => 0.00,
            'billing_cycle' => 'monthly',
        ]);

        $resolver = app(AddonResolverService::class);
        $cacheKey = "catalog:addons:package_{$package->id}";

        $this->assertFalse(Cache::has($cacheKey));

        // First call populates cache
        $resolved = $resolver->getResolvedAddonsForPackage($package);
        $this->assertTrue(Cache::has($cacheKey));
        $this->assertTrue($resolved->has('os'));

        // Updating addon must purge cache
        $addon->update(['name' => 'Ubuntu 24.04 LTS Updated']);
        $this->assertFalse(Cache::has($cacheKey), 'Package addon cache should be cleared on addon update');

        // Subsequent call re-populates
        $freshAddons = $resolver->getResolvedAddonsForPackage($package);
        $this->assertTrue(Cache::has($cacheKey));
        $this->assertEquals('Ubuntu 24.04 LTS Updated', $freshAddons->get('os')->first()->name);
    }

    public function test_sitemap_xml_is_cached_with_cache_control_headers()
    {
        Package::create([
            'name' => 'Sitemap VPS',
            'description' => 'Sitemap Test',
            'category' => 'vps',
            'price_monthly' => 15.00,
            'is_active' => true,
        ]);

        $this->assertFalse(Cache::has('catalog:sitemap_xml'));

        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/xml; charset=UTF-8');
        $response->assertHeader('Cache-Control', 'max-age=3600, public');

        // Verify cache was populated
        $this->assertTrue(Cache::has('catalog:sitemap_xml'));
    }
}
