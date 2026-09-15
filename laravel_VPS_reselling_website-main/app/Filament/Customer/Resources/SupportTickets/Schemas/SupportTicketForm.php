<?php

namespace App\Filament\Customer\Resources\SupportTickets\Schemas;

use App\Models\Service;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupportTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('subject')
                    ->label('Ticket Subject')
                    ->placeholder('e.g. Reverse DNS PTR configuration request')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Select::make('department')
                    ->label('Department')
                    ->options([
                        'technical' => '🛠️ Technical Support',
                        'billing' => '💳 Billing & Invoices',
                        'sales' => '💼 Sales & Upgrades',
                    ])
                    ->default('technical')
                    ->required(),

                Select::make('priority')
                    ->label('Priority')
                    ->options([
                        'low' => '🟢 Low — General inquiry',
                        'medium' => '🟡 Medium — Standard assistance',
                        'high' => '🔴 High — Service disruption / Urgent',
                    ])
                    ->default('medium')
                    ->required(),

                Select::make('service_id')
                    ->label('Associated Server (Optional)')
                    ->options(function () {
                        return Service::where('user_id', auth()->id())
                            ->get()
                            ->mapWithKeys(function ($service) {
                                $name = $service->server_name ?: ('VPS #' . $service->id);
                                $ip = $service->ip_address ?: 'Pending IP';
                                return [$service->id => "{$name} ({$ip})"];
                            });
                    })
                    ->placeholder('Select a server if this ticket relates to a specific VPS')
                    ->columnSpanFull(),

                Textarea::make('initial_message')
                    ->label('Message / Problem Description')
                    ->placeholder('Please describe your issue, steps taken, or requested changes in detail...')
                    ->rows(6)
                    ->required()
                    ->columnSpanFull()
                    ->visibleOn('create'),
            ]);
    }
}
