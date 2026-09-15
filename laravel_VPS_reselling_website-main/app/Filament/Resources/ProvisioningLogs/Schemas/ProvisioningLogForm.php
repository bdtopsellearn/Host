<?php

namespace App\Filament\Resources\ProvisioningLogs\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProvisioningLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('API Request Details')
                    ->schema([
                        Select::make('service_id')
                            ->label('Related Service ID')
                            ->relationship('service', 'id')
                            ->disabled(),
                        TextInput::make('action')
                            ->label('API Action')
                            ->disabled(),
                        Toggle::make('is_success')
                            ->label('Status (Success)')
                            ->disabled(),
                    ])->columns(3),
                    
                Section::make('Payload Details')
                    ->schema([
                        \Filament\Forms\Components\Textarea::make('request_payload')
                            ->label('Request Payload')
                            ->disabled()
                            ->rows(10)
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $state)
                            ->columnSpanFull(),
                        \Filament\Forms\Components\Textarea::make('response_payload')
                            ->label('Response Payload / Error Details')
                            ->disabled()
                            ->rows(10)
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $state)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
