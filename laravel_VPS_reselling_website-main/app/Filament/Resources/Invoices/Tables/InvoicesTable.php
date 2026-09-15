<?php

namespace App\Filament\Resources\Invoices\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Issued At')
                    ->dateTime('M d, Y H:i:s')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('order.order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->placeholder('N/A')
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->user?->email ?? '')
                    ->toggleable(),
                TextColumn::make('total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('amount')
                    ->label('Subtotal')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
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
                    })
                    ->toggleable(),
                TextColumn::make('paid_at')
                    ->label('Paid At')
                    ->dateTime('M d, Y H:i:s')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'crypto' => 'info',
                        'stripe' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => strtoupper($state ?? 'N/A'))
                    ->toggleable(),
                TextColumn::make('crypto_network')
                    ->label('Network')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'usdt_trc20' => 'USDT (Tron)',
                        'usdc_polygon' => 'USDC (Polygon)',
                        'usdt_polygon' => 'USDT (Polygon)',
                        default => $state ? strtoupper($state) : '-',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('crypto_txid')
                    ->label('TxID / Hash')
                    ->placeholder('-')
                    ->copyable()
                    ->limit(14)
                    ->tooltip(fn ($record) => $record->crypto_txid)
                    ->toggleable(),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('M d, Y')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->columnToggleFormColumns(2)
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'paid' => 'Paid',
                        'pending' => 'Pending (Unpaid)',
                        'refunded' => 'Refunded',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('payment_method')
                    ->options([
                        'stripe' => 'Stripe (Credit / Debit Card)',
                        'crypto' => 'Cryptocurrency',
                    ]),
                SelectFilter::make('crypto_network')
                    ->options([
                        'usdt_trc20' => 'USDT (Tron TRC20)',
                        'usdc_polygon' => 'USDC (Polygon)',
                        'usdt_polygon' => 'USDT (Polygon)',
                    ]),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Issued From'),
                        DatePicker::make('created_until')->label('Issued Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                Action::make('export')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function ($livewire): StreamedResponse {
                        $records = $livewire->getFilteredTableQuery()->with(['user', 'order'])->get();
                        $filename = 'invoices-export-' . now()->format('Y-m-d_His') . '.csv';

                        return response()->streamDownload(function () use ($records) {
                            $file = fopen('php://output', 'w');
                            fputs($file, "\xEF\xBB\xBF");
                            fputcsv($file, [
                                'Invoice #',
                                'Issued At',
                                'Order #',
                                'Customer Name',
                                'Customer Email',
                                'Subtotal Amount',
                                'Total Amount',
                                'Status',
                                'Paid At',
                                'Payment Method',
                                'Crypto TxID',
                                'Due Date',
                                'Crypto Network',
                            ]);

                            foreach ($records as $record) {
                                fputcsv($file, [
                                    $record->invoice_number,
                                    $record->created_at?->toIso8601String(),
                                    $record->order?->order_number ?? 'N/A',
                                    $record->user?->name ?? 'N/A',
                                    $record->user?->email ?? 'N/A',
                                    number_format((float) ($record->amount ?? 0), 2, '.', ''),
                                    number_format((float) $record->total, 2, '.', ''),
                                    $record->status,
                                    $record->paid_at ? $record->paid_at->toIso8601String() : 'N/A',
                                    strtoupper($record->payment_method ?? 'N/A'),
                                    $record->crypto_txid ?? 'N/A',
                                    $record->due_date ? $record->due_date->format('Y-m-d') : 'N/A',
                                    $record->crypto_network ?? 'N/A',
                                ]);
                            }
                            fclose($file);
                        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
                    }),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
