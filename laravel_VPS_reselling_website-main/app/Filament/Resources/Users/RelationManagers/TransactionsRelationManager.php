<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    protected static ?string $title = 'Transactions';

    protected static string | \BackedEnum | null $icon = 'heroicon-o-credit-card';

    public static function getBadge(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): ?string
    {
        $count = $ownerRecord->invoices()
            ->where(fn (Builder $q) => $q->whereNotNull('crypto_txid')->orWhereNotNull('stripe_payment_intent_id')->orWhere('status', 'paid'))
            ->count();
        return $count > 0 ? (string) $count : null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(null)
            ->modifyQueryUsing(fn (Builder $query) => $query->where(fn (Builder $q) => $q->whereNotNull('crypto_txid')->orWhereNotNull('stripe_payment_intent_id')->orWhere('status', 'paid')))
            ->recordTitleAttribute('invoice_number')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('transaction_ref')
                    ->label('Transaction ID / Hash')
                    ->state(fn (Invoice $record) => $record->crypto_txid ?: ($record->stripe_payment_intent_id ?: "INV-PAY-{$record->id}"))
                    ->copyable()
                    ->limit(20)
                    ->weight('bold')
                    ->url(fn (Invoice $record) => !empty($record->crypto_txid)
                        ? ((str_starts_with($record->crypto_txid, '0x') || str_contains($record->crypto_network ?? '', 'polygon'))
                            ? "https://polygonscan.com/tx/{$record->crypto_txid}"
                            : "https://tronscan.org/#/transaction/{$record->crypto_txid}")
                        : null, true)
                    ->icon(fn (Invoice $record) => !empty($record->crypto_txid) ? 'heroicon-m-arrow-top-right-on-square' : null)
                    ->iconPosition('after')
                    ->toggleable(),
                TextColumn::make('paid_at')
                    ->label('Date & Time')
                    ->dateTime('M d, Y H:i:s')
                    ->placeholder(fn (Invoice $record) => $record->created_at?->format('M d, Y H:i:s') ?? '-')
                    ->toggleable(),
                TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->badge()
                    ->color('primary')
                    ->toggleable(),
                TextColumn::make('payment_method')
                    ->label('Gateway')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'crypto' => 'info',
                        'stripe' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(function (Invoice $record): string {
                        $method = strtoupper($record->payment_method ?? 'PAYMENT');
                        if ($record->crypto_network) {
                            $net = match ($record->crypto_network) {
                                'usdt_trc20' => 'USDT (Tron)',
                                'usdc_polygon' => 'USDC (Polygon)',
                                'usdt_polygon' => 'USDT (Polygon)',
                                default => strtoupper($record->crypto_network),
                            };
                            return "{$method} - {$net}";
                        }
                        return $method;
                    })
                    ->toggleable(),
                TextColumn::make('total')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending', 'unpaid' => 'warning',
                        'refunded' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->toggleable(),
            ])
            ->columnToggleFormColumns(2)
            ->filters([
                SelectFilter::make('payment_method')
                    ->label('Gateway')
                    ->options([
                        'stripe' => 'Stripe / Card',
                        'crypto' => 'Cryptocurrency',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'paid' => 'Paid',
                        'pending' => 'Pending',
                        'refunded' => 'Refunded',
                        'cancelled' => 'Cancelled',
                    ]),
                Filter::make('paid_at')
                    ->form([
                        DatePicker::make('paid_from')->label('Paid From'),
                        DatePicker::make('paid_until')->label('Paid Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['paid_from'], fn ($q, $date) => $q->whereDate('paid_at', '>=', $date))
                            ->when($data['paid_until'], fn ($q, $date) => $q->whereDate('paid_at', '<=', $date));
                    }),
            ])
            ->filtersFormColumns(2)
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Invoice $record): string => InvoiceResource::getUrl('view', ['record' => $record])),
            ])
            ->toolbarActions([
                Action::make('export')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function ($livewire): StreamedResponse {
                        $records = $livewire->getFilteredTableQuery()->get();
                        $filename = 'customer-transactions-' . now()->format('Y-m-d_His') . '.csv';

                        return response()->streamDownload(function () use ($records) {
                            $file = fopen('php://output', 'w');
                            fputs($file, "\xEF\xBB\xBF");
                            fputcsv($file, ['TxID / Hash', 'Date & Time', 'Invoice #', 'Gateway', 'Amount', 'Status']);

                            foreach ($records as $record) {
                                fputcsv($file, [
                                    $record->crypto_txid ?: ($record->stripe_payment_intent_id ?: "INV-PAY-{$record->id}"),
                                    $record->paid_at?->format('Y-m-d H:i:s') ?? ($record->created_at?->format('Y-m-d H:i:s') ?? '-'),
                                    $record->invoice_number,
                                    strtoupper($record->payment_method ?? 'N/A'),
                                    number_format((float) $record->total, 2, '.', ''),
                                    $record->status,
                                ]);
                            }
                            fclose($file);
                        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
                    }),
            ]);
    }
}
