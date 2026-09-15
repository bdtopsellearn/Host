<?php

namespace App\Filament\Resources\SupportTickets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupportTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('subject')
                    ->required(),
                Select::make('department')
                    ->options(['billing' => 'Billing', 'technical' => 'Technical', 'sales' => 'Sales'])
                    ->required(),
                Select::make('priority')
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'])
                    ->required(),
                Select::make('status')
                    ->options([
            'open' => 'Open',
            'in_progress' => 'In progress',
            'answered' => 'Answered',
            'closed' => 'Closed',
        ])
                    ->required(),
            ]);
    }
}
