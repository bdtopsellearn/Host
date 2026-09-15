<?php

namespace App\Filament\Customer\Resources\Services\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('order_id')
                    ->required()
                    ->numeric(),
                TextInput::make('package_id')
                    ->required()
                    ->numeric(),
                TextInput::make('contabo_instance_id'),
                TextInput::make('ip_address'),
                Textarea::make('encrypted_credentials')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
            'awaiting_provisioning' => 'Awaiting provisioning',
            'provisioning_failed' => 'Provisioning failed',
            'active' => 'Active',
            'suspended' => 'Suspended',
            'terminated' => 'Terminated',
        ])
                    ->required(),
                DatePicker::make('next_due_date'),
                TextInput::make('billing_cycle')
                    ->required(),
            ]);
    }
}
