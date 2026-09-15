<?php

namespace App\Filament\Resources\Invoices\Infolists;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Invoice Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('invoice_number')
                                    ->label('Invoice #')
                                    ->weight('bold')
                                    ->copyable()
                                    ->icon('heroicon-o-document-text'),
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'paid' => 'success',
                                        'pending', 'unpaid' => 'warning',
                                        'refunded' => 'info',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'pending', 'unpaid' => 'Pending (Unpaid)',
                                        'paid' => 'Paid',
                                        'refunded' => 'Refunded',
                                        'cancelled' => 'Cancelled',
                                        default => ucfirst($state),
                                    }),
                                TextEntry::make('created_at')
                                    ->label('Invoice Date')
                                    ->dateTime()
                                    ->icon('heroicon-o-calendar-days'),
                                TextEntry::make('due_date')
                                    ->label('Due Date')
                                    ->date()
                                    ->icon('heroicon-o-calendar'),
                                TextEntry::make('paid_at')
                                    ->label('Paid At')
                                    ->dateTime()
                                    ->placeholder('Not Paid')
                                    ->icon('heroicon-o-banknotes'),
                                TextEntry::make('order.order_number')
                                    ->label('Associated Order')
                                    ->placeholder('No Order')
                                    ->icon('heroicon-o-shopping-bag')
                                    ->badge()
                                    ->color('primary')
                                    ->suffixAction(
                                        \Filament\Actions\Action::make('view_order')
                                            ->icon('heroicon-m-arrow-top-right-on-square')
                                            ->tooltip('View Order Details')
                                            ->visible(fn ($record) => !empty($record->order_id))
                                            ->url(fn ($record) => \App\Filament\Resources\Orders\OrderResource::getUrl('view', ['record' => $record->order_id]))
                                    ),
                            ]),
                    ]),

                Section::make('Customer Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Customer Name')
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('user.email')
                                    ->label('Customer Email')
                                    ->icon('heroicon-o-envelope')
                                    ->copyable(),
                            ]),
                    ]),

                Section::make('Financial Summary')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('amount')
                                    ->label('Main Subtotal')
                                    ->money('USD'),
                                TextEntry::make('tax')
                                    ->label('Tax')
                                    ->money('USD')
                                    ->placeholder('$0.00'),
                                TextEntry::make('total')
                                    ->label('Total Amount')
                                    ->weight('bold')
                                    ->color('success')
                                    ->money('USD')
                                    ->icon('heroicon-o-currency-dollar'),
                            ]),
                    ]),

                Section::make('Payment & Settlement Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('payment_method')
                                    ->label('Payment Method')
                                    ->badge()
                                    ->color(fn (?string $state): string => match ($state) {
                                        'crypto' => 'info',
                                        'stripe' => 'success',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (?string $state): string => strtoupper($state ?? 'Pending')),

                                TextEntry::make('crypto_network')
                                    ->label('Crypto Network')
                                    ->badge()
                                    ->color('warning')
                                    ->placeholder('N/A')
                                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                                        'usdt_trc20' => 'USDT (Tron TRC-20)',
                                        'usdc_polygon' => 'USDC (Polygon PoS)',
                                        'usdt_polygon' => 'USDT (Polygon PoS)',
                                        default => $state ?? 'N/A',
                                    }),

                                TextEntry::make('crypto_txid')
                                    ->label('Transaction Proof (TxID)')
                                    ->copyable()
                                    ->placeholder('Awaiting Submission')
                                    ->suffixAction(
                                        \Filament\Actions\Action::make('open_explorer')
                                            ->icon('heroicon-m-arrow-top-right-on-square')
                                            ->tooltip('Open on Blockchain Explorer')
                                            ->visible(fn ($record) => !empty($record->crypto_txid))
                                            ->url(fn ($record) => (str_starts_with($record->crypto_txid ?? '', '0x') || str_contains($record->crypto_network ?? '', 'polygon'))
                                                ? "https://polygonscan.com/tx/{$record->crypto_txid}"
                                                : "https://tronscan.org/#/transaction/{$record->crypto_txid}", true)
                                    ),

                                TextEntry::make('stripe_payment_intent_id')
                                    ->label('Stripe Payment ID')
                                    ->placeholder('N/A')
                                    ->copyable()
                                    ->icon('heroicon-o-credit-card'),
                            ]),
                    ]),
            ]);
    }
}
