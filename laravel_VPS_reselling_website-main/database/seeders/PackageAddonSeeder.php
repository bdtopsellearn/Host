<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\PackageAddon;
use Illuminate\Database\Seeder;

class PackageAddonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. LAYER 1: Global Base Addons (Default Catalog for all Packages)
        $globalAddons = [
            // Operating Systems (Contabo imageId mappings)
            // Ubuntu Family
            [
                'type' => 'os',
                'category' => 'ubuntu',
                'name' => 'Ubuntu 24.04 LTS',
                'value' => 'ubuntu_24_04',
                'api_identifier' => 'ubuntu-24.04-x86_64',
                'price' => 0.00,
                'sort_order' => 1,
            ],
            [
                'type' => 'os',
                'category' => 'ubuntu',
                'name' => 'Ubuntu 22.04 LTS',
                'value' => 'ubuntu_22_04',
                'api_identifier' => 'ubuntu-22.04-x86_64',
                'price' => 0.00,
                'sort_order' => 2,
            ],
            [
                'type' => 'os',
                'category' => 'ubuntu',
                'name' => 'Ubuntu 20.04 LTS',
                'value' => 'ubuntu_20_04',
                'api_identifier' => 'ubuntu-20.04-x86_64',
                'price' => 0.00,
                'sort_order' => 3,
            ],

            // Debian Family
            [
                'type' => 'os',
                'category' => 'debian',
                'name' => 'Debian 12',
                'value' => 'debian_12',
                'api_identifier' => 'debian-12-x86_64',
                'price' => 0.00,
                'sort_order' => 1,
            ],
            [
                'type' => 'os',
                'category' => 'debian',
                'name' => 'Debian 11',
                'value' => 'debian_11',
                'api_identifier' => 'debian-11-x86_64',
                'price' => 0.00,
                'sort_order' => 2,
            ],

            // RHEL Variants (AlmaLinux & Rocky Linux)
            [
                'type' => 'os',
                'category' => 'rhel',
                'name' => 'AlmaLinux 10',
                'value' => 'almalinux_10',
                'api_identifier' => 'almalinux-10-x86_64',
                'price' => 0.00,
                'sort_order' => 1,
            ],
            [
                'type' => 'os',
                'category' => 'rhel',
                'name' => 'Rocky Linux 10',
                'value' => 'rocky_10',
                'api_identifier' => 'rocky-linux-10-x86_64',
                'price' => 0.00,
                'sort_order' => 2,
            ],
            [
                'type' => 'os',
                'category' => 'rhel',
                'name' => 'AlmaLinux 9',
                'value' => 'almalinux_9',
                'api_identifier' => 'almalinux-9-x86_64',
                'price' => 0.00,
                'sort_order' => 3,
            ],
            [
                'type' => 'os',
                'category' => 'rhel',
                'name' => 'Rocky Linux 9',
                'value' => 'rocky_9',
                'api_identifier' => 'rocky-linux-9-x86_64',
                'price' => 0.00,
                'sort_order' => 4,
            ],

            // Windows-Server (with Datacenter licensing)
            [
                'type' => 'os',
                'category' => 'windows',
                'name' => 'Windows Server Datacenter 2025',
                'value' => 'win_2025',
                'api_identifier' => 'windows-2025-datacenter',
                'price' => 17.00,
                'sort_order' => 1,
            ],
            [
                'type' => 'os',
                'category' => 'windows',
                'name' => 'Windows Server Datacenter 2022',
                'value' => 'win_2022',
                'api_identifier' => 'windows-2022-standard',
                'price' => 17.00,
                'sort_order' => 2,
            ],
            [
                'type' => 'os',
                'category' => 'windows',
                'name' => 'Windows Server Datacenter 2019',
                'value' => 'win_2019',
                'api_identifier' => 'windows-2019-datacenter',
                'price' => 17.00,
                'sort_order' => 3,
            ],
            [
                'type' => 'os',
                'category' => 'windows',
                'name' => 'Windows Server Datacenter 2016',
                'value' => 'win_2016',
                'api_identifier' => 'windows-2016-datacenter',
                'price' => 17.00,
                'sort_order' => 4,
            ],

            // Datacenter Regions (Contabo region codes & latencies)
            [
                'type' => 'region',
                'name' => 'EU Central (Germany)',
                'value' => 'eu_central',
                'api_identifier' => 'EU',
                'price' => 0.00,
                'sort_order' => 1,
            ],
            [
                'type' => 'region',
                'name' => 'UK (London)',
                'value' => 'uk_london',
                'api_identifier' => 'UK',
                'price' => 0.00,
                'sort_order' => 2,
            ],
            [
                'type' => 'region',
                'name' => 'US East (New York)',
                'value' => 'us_east',
                'api_identifier' => 'US-central',
                'price' => 1.50,
                'sort_order' => 3,
            ],
            [
                'type' => 'region',
                'name' => 'US West (Seattle)',
                'value' => 'us_west',
                'api_identifier' => 'US-west',
                'price' => 1.50,
                'sort_order' => 4,
            ],
            [
                'type' => 'region',
                'name' => 'Asia (Singapore)',
                'value' => 'asia_singapore',
                'api_identifier' => 'SIN',
                'price' => 2.50,
                'sort_order' => 5,
            ],
            [
                'type' => 'region',
                'name' => 'Australia (Sydney)',
                'value' => 'au_sydney',
                'api_identifier' => 'AUS',
                'price' => 3.00,
                'sort_order' => 6,
            ],

            // Primary NVMe Storage Tiers
            [
                'type' => 'storage',
                'name' => '100 GB NVMe',
                'value' => '100GB',
                'api_identifier' => '100GB-NVME',
                'price' => 0.00,
                'sort_order' => 1,
            ],
            [
                'type' => 'storage',
                'name' => '200 GB NVMe',
                'value' => '200GB',
                'api_identifier' => '200GB-NVME',
                'price' => 1.80,
                'sort_order' => 2,
            ],
            [
                'type' => 'storage',
                'name' => '400 GB NVMe',
                'value' => '400GB',
                'api_identifier' => '400GB-NVME',
                'price' => 3.60,
                'sort_order' => 3,
            ],

            // High-Availability Add-Ons
            [
                'type' => 'backup',
                'name' => 'Auto Backup',
                'value' => '1',
                'api_identifier' => 'addon-autobackup',
                'price' => 1.95,
                'sort_order' => 1,
            ],
            [
                'type' => 'network',
                'name' => 'Private Networking Enabled',
                'value' => '1',
                'api_identifier' => 'addon-private-vpc',
                'price' => 2.75,
                'sort_order' => 1,
            ],
        ];

        foreach ($globalAddons as $addon) {
            PackageAddon::updateOrCreate(
                [
                    'package_id' => null,
                    'type' => $addon['type'],
                    'value' => $addon['value'],
                ],
                [
                    'category' => $addon['category'] ?? null,
                    'name' => $addon['name'],
                    'api_identifier' => $addon['api_identifier'],
                    'price' => $addon['price'],
                    'is_global' => true,
                    'is_out_of_stock' => false,
                    'is_enabled' => true,
                    'sort_order' => $addon['sort_order'],
                    'billing_cycle' => 'monthly',
                ]
            );
        }

        // 2. LAYER 2: Package-Specific Overrides
        // Example A: Cloud VPS 8 (ID: 3 or by name) has a custom Windows licensing price
        $package8 = Package::where('name', 'Cloud VPS 8')->first() ?? Package::find(3);
        if ($package8) {
            PackageAddon::updateOrCreate(
                [
                    'package_id' => $package8->id,
                    'type' => 'os',
                    'value' => 'win_2022',
                ],
                [
                    'category' => 'windows',
                    'name' => 'Windows Server Datacenter 2022',
                    'api_identifier' => 'windows-2022-standard',
                    'price' => 17.00,
                    'is_global' => false,
                    'is_out_of_stock' => false,
                    'is_enabled' => true,
                    'sort_order' => 2,
                    'billing_cycle' => 'monthly',
                ]
            );
        }

        // Example B: Entry level Cloud VPS 4 (ID: 1 or by name) excludes Australia datacenter (disabled for this tier)
        $package4 = Package::where('name', 'Cloud VPS 4')->first() ?? Package::find(1);
        if ($package4) {
            PackageAddon::updateOrCreate(
                [
                    'package_id' => $package4->id,
                    'type' => 'region',
                    'value' => 'au_sydney',
                ],
                [
                    'name' => 'Australia (Sydney)',
                    'api_identifier' => 'AUS',
                    'price' => 3.00,
                    'is_global' => false,
                    'is_out_of_stock' => false,
                    'is_enabled' => false, // Excluded from Cloud VPS 4
                    'sort_order' => 6,
                    'billing_cycle' => 'monthly',
                ]
            );
        }
    }
}
