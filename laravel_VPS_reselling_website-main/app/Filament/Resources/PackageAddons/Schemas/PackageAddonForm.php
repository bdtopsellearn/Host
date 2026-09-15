<?php

namespace App\Filament\Resources\PackageAddons\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PackageAddonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('is_global')
                    ->label('Global Addon (Layer 1 Base)')
                    ->helperText('Enable to make this addon available as default across all packages')
                    ->default(true)
                    ->required(),
                Select::make('package_id')
                    ->label('Target Package (Layer 2 Override)')
                    ->helperText('Leave empty for Global Base default; select a package to create a tier-specific override')
                    ->relationship('package', 'name')
                    ->nullable(),
                Toggle::make('is_enabled')
                    ->label('Active / Available')
                    ->helperText('Turn off to completely exclude or disable this addon on the selected package')
                    ->default(true)
                    ->required(),
                Toggle::make('is_out_of_stock')
                    ->label('Mark as Out of Stock')
                    ->helperText('Displays "Sold Out" and prevents checkout selection')
                    ->default(false)
                    ->required(),
                Select::make('type')
                    ->label('Addon Type')
                    ->options([
                        'os' => 'Operating System (OS)',
                        'region' => 'Datacenter Region',
                        'storage' => 'NVMe Storage Tier',
                        'backup' => 'Backup Protection',
                        'network' => 'Private Networking VPC',
                        'feature' => 'General Feature',
                    ])
                    ->required(),
                Select::make('category')
                    ->label('OS Family / Distribution')
                    ->options([
                        'ubuntu' => 'Ubuntu',
                        'debian' => 'Debian',
                        'rhel' => 'RHEL Variants (AlmaLinux / Rocky)',
                        'windows' => 'Windows-Server',
                    ])
                    ->helperText('Grouping family used for Contabo-style popup modals')
                    ->nullable(),
                TextInput::make('name')
                    ->label('Display Name')
                    ->placeholder('e.g. Windows Server 2022, US East (New York)')
                    ->required(),
                TextInput::make('value')
                    ->label('System Slug / Value')
                    ->placeholder('e.g. win_2022, us_east, 100GB, 1')
                    ->required(),
                TextInput::make('api_identifier')
                    ->label('Contabo API Identifier')
                    ->placeholder('e.g. windows-2022-standard, US-central, 100GB-NVME')
                    ->helperText('Exact slug required by Contabo API during instance provisioning'),
                TextInput::make('price')
                    ->label('Monthly Retail Price')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->default(0.00),
                TextInput::make('sort_order')
                    ->label('Display Sort Order')
                    ->numeric()
                    ->default(0),
                Select::make('billing_cycle')
                    ->label('Billing Cycle')
                    ->options([
                        'monthly' => 'Monthly',
                        'one_time' => 'One time',
                    ])
                    ->default('monthly')
                    ->required(),
            ]);
    }
}
