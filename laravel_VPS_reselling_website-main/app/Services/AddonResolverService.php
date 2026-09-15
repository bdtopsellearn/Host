<?php

namespace App\Services;

use App\Models\Package;
use App\Models\PackageAddon;
use Illuminate\Support\Collection;

class AddonResolverService
{
    /**
     * Resolve all addons for a given package using the 2-Layer Override Hierarchy.
     * Cached in memory for 24 hours to eliminate repetitive database queries.
     *
     * @param Package|null $package
     * @return Collection<string, Collection<int, PackageAddon>>
     */
    public function getResolvedAddonsForPackage(?Package $package = null): Collection
    {
        $cacheKey = $package ? "catalog:addons:package_{$package->id}" : 'catalog:addons:global';

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 86400, function () use ($package) {
            return $this->resolveAddonsHierarchy($package);
        });
    }

    /**
     * Direct database resolution of the 2-Layer Override Hierarchy.
     */
    protected function resolveAddonsHierarchy(?Package $package = null): Collection
    {
        // 1. Fetch Global Base Addons (Layer 1)
        $globalAddons = PackageAddon::global()->get();

        // 2. Fetch Package-Specific Overrides (Layer 2) if package is provided
        $overrides = $package 
            ? PackageAddon::where('package_id', $package->id)->get()->keyBy(fn($item) => $item->type . ':' . $item->value)
            : collect();


        $resolved = collect();

        foreach ($globalAddons as $global) {
            $key = $global->type . ':' . $global->value;

            if ($overrides->has($key)) {
                $override = $overrides->get($key);

                // If explicitly disabled for this package, do not include in available addons
                if (!$override->is_enabled) {
                    continue;
                }

                // Clone the override object and ensure display properties
                $item = clone $override;
                if (empty($item->api_identifier)) {
                    $item->api_identifier = $global->api_identifier;
                }
                $resolved->push($item);
            } else {
                // If global itself is disabled, skip
                if (!$global->is_enabled) {
                    continue;
                }
                $resolved->push(clone $global);
            }
        }

        // Add any package-specific exclusive addons that don't exist in global
        foreach ($overrides as $key => $override) {
            if (!$globalAddons->contains(fn($g) => ($g->type . ':' . $g->value) === $key)) {
                if ($override->is_enabled) {
                    $resolved->push(clone $override);
                }
            }
        }

        // Sort and group by type
        return $resolved
            ->sortBy('sort_order')
            ->groupBy('type');
    }

    /**
     * Calculate the total addon cost for a selected set of addon values on a package.
     *
     * @param Package $package
     * @param array $selectedValues Array of addon values (e.g. ['ubuntu_24_04', 'us_east', '200GB', '1'])
     * @return array ['total_monthly' => float, 'addons' => Collection]
     */
    public function calculateAddonsTotal(Package $package, array $selectedValues): array
    {
        $resolved = $this->getResolvedAddonsForPackage($package)->flatten();
        $selectedAddons = collect();
        $monthlyTotal = 0.0;

        foreach (array_filter($selectedValues) as $val) {
            $addon = $resolved->first(fn($a) => (string) $a->value === (string) $val);
            if ($addon) {
                $selectedAddons->push($addon);
                $monthlyTotal += (float) $addon->price;
            }
        }

        return [
            'total_monthly' => round($monthlyTotal, 2),
            'addons' => $selectedAddons,
        ];
    }
}
