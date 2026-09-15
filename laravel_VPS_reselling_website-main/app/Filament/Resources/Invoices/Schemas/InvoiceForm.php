<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('order_id')
                    ->numeric(),
                TextInput::make('invoice_number')
                    ->default(fn () => \App\Models\Invoice::generateNextNumber())
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('tax')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('total')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options(['pending' => 'Pending (Unpaid)', 'paid' => 'Paid', 'refunded' => 'Refunded', 'cancelled' => 'Cancelled'])
                    ->required(),
                TextInput::make('stripe_payment_intent_id'),
                DatePicker::make('due_date'),
                DateTimePicker::make('paid_at'),
            ]);
    }
}
