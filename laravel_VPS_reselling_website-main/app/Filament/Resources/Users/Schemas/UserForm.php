<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('company_name')
                    ->label('Company / Organization'),
                TextInput::make('phone')
                    ->label('Phone Number')
                    ->tel(),
                TextInput::make('address')
                    ->label('Street Address'),
                TextInput::make('city')
                    ->label('City'),
                TextInput::make('state')
                    ->label('State / Province'),
                TextInput::make('country')
                    ->label('Country'),
                TextInput::make('zip_code')
                    ->label('Postal / Zip Code'),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
            ]);
    }
}
