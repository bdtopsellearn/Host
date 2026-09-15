<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Service & Billing Information')
                    ->schema([
                        Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        Select::make('package_id')
                            ->label('Package Plan')
                            ->relationship('package', 'name')
                            ->searchable()
                            ->required(),
                        Select::make('status')
                            ->label('Service Status')
                            ->options([
                                'awaiting_provisioning' => 'Awaiting Provisioning',
                                'provisioned' => 'Provisioned',
                                'active' => 'Active',
                                'suspended' => 'Suspended',
                                'terminated' => 'Terminated',
                                'provisioning_failed' => 'Provisioning Failed',
                            ])
                            ->required(),
                        Select::make('billing_cycle')
                            ->label('Billing Cycle')
                            ->options([
                                'monthly' => 'Monthly',
                                'quarterly' => 'Quarterly',
                                'semi_annually' => 'Semi-Annually',
                                'annually' => 'Annually',
                                'biennially' => 'Biennially (24 Months)',
                            ])
                            ->required(),
                        TextInput::make('recurring_amount')
                            ->label('Recurring Renewal Rate')
                            ->prefix('$')
                            ->numeric()
                            ->helperText('Amount charged to the client on renewal invoice generation. Admin can adjust this rate at any time.')
                            ->required(),
                        DatePicker::make('next_due_date')
                            ->label('Next Renewal / Due Date'),
                    ])
                    ->columns(2),

                Section::make('Contabo Instance & Server Details')
                    ->schema([
                        TextInput::make('contabo_instance_id')
                            ->label('Contabo Instance ID')
                            ->helperText('Numeric instance ID returned by Contabo API'),
                        TextInput::make('ip_address')
                            ->label('Primary IP Address'),
                    ])
                    ->columns(2),

                Section::make('Hardware & Provisioning Snapshot')
                    ->description('Frozen hardware specs and active addons configured at checkout/upgrade time.')
                    ->schema([
                        KeyValue::make('specs_snapshot')
                            ->label('Hardware Specs Snapshot')
                            ->helperText('Cores, RAM, Storage tier, OS, and Region snapshot')
                            ->columnSpanFull(),
                        Textarea::make('active_addons')
                            ->label('Active Addons JSON Payload')
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state)
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
