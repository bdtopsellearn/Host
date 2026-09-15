<?php

namespace App\Filament\Resources\Packages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Package Name')
                            ->placeholder('e.g. Cloud VPS 4')
                            ->required(),
                        Select::make('category')
                            ->label('Category')
                            ->options([
                                'vps' => 'VPS (Virtual Private Server)',
                                'rdp' => 'RDP (Remote Desktop)',
                            ])
                            ->required(),
                        TextInput::make('contabo_product_id')
                            ->label('Contabo Product ID')
                            ->placeholder('e.g. V1, V2, VPS 4')
                            ->helperText('Product identifier used by Contabo Provisioning API'),
                        Toggle::make('is_active')
                            ->label('Active / Visible to Customers')
                            ->default(true)
                            ->required(),
                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Brief overview of this package...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Hardware Specifications')
                    ->description('Specify server resources that will be displayed on the pricing tables and checkout page')
                    ->schema([
                        TextInput::make('specs.cores')
                            ->label('CPU Cores')
                            ->placeholder('e.g. 4')
                            ->helperText('Number of vCPU Cores')
                            ->required(),
                        TextInput::make('specs.memory')
                            ->label('RAM / Memory')
                            ->placeholder('e.g. 8 GB')
                            ->helperText('RAM capacity (e.g. 8 GB)')
                            ->required(),
                        TextInput::make('specs.storage')
                            ->label('Storage / Disk')
                            ->placeholder('e.g. 100 GB SSD')
                            ->helperText('Storage size and type (e.g. 100 GB SSD)')
                            ->required(),
                        TextInput::make('specs.bandwidth')
                            ->label('Bandwidth / Port Speed')
                            ->placeholder('e.g. 200 Mbit/s Port')
                            ->helperText('Included network port or monthly bandwidth limit'),
                        TextInput::make('specs.snapshots')
                            ->label('Snapshots')
                            ->placeholder('e.g. 1 Snapshot')
                            ->helperText('Number of snapshots included'),
                    ])
                    ->columns(2),

                Section::make('Pricing & Billing Plans')
                    ->description('Set package pricing for various billing intervals (USD)')
                    ->schema([
                        TextInput::make('price_monthly')
                            ->label('Monthly Price ($)')
                            ->prefix('$')
                            ->numeric()
                            ->required(),
                        TextInput::make('price_quarterly')
                            ->label('Quarterly Price ($)')
                            ->prefix('$')
                            ->numeric(),
                        TextInput::make('price_semi_annually')
                            ->label('Semi-Annually Price ($)')
                            ->prefix('$')
                            ->numeric(),
                        TextInput::make('price_annually')
                            ->label('Annually Price ($)')
                            ->prefix('$')
                            ->numeric(),
                        TextInput::make('setup_fee')
                            ->label('Setup Fee ($)')
                            ->prefix('$')
                            ->numeric()
                            ->default(0.00)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
