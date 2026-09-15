<?php

namespace App\Filament\Customer\Resources\Invoices\Tables;

use App\Models\Invoice;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->weight(FontWeight::Bold)
                    ->copyable()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('order.order_number')
                    ->label('Order #')
                    ->badge()
                    ->color('gray')
                    ->placeholder('N/A')
                    ->copyable()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Invoice Date')
                    ->date('M j, Y')
                    ->sortable(),

                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('M j, Y')
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->weight(FontWeight::Bold)
                    ->formatStateUsing(fn ($state) => '$' . number_format((float) $state, 2) . ' USD')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending', 'unpaid' => 'danger',
                        'refunded' => 'info',
                        'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending', 'unpaid' => 'Unpaid',
                        'paid' => 'Paid',
                        'refunded' => 'Refunded',
                        'cancelled' => 'Cancelled',
                        default => ucfirst($state),
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (Invoice $record) => \App\Filament\Customer\Resources\Invoices\InvoiceResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                ViewAction::make()
                    ->label('View')
                    ->color('gray'),
            ]);
    }
}
