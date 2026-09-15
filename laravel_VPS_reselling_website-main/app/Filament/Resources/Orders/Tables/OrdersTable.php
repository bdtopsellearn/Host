<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
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

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Placed At')
                    ->dateTime('M d, Y H:i:s')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Order $record) => $record->user->email ?? '')
                    ->toggleable(),
                TextColumn::make('services.package.name')
                    ->label('Package')
                    ->badge()
                    ->color('primary')
                    ->toggleable(),
                TextColumn::make('invoice.invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->placeholder('N/A')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('invoice.payment_method')
                    ->label('Method')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'crypto' => 'info',
                        'stripe' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => strtoupper($state ?? 'N/A'))
                    ->toggleable(),
                TextColumn::make('invoice.status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending', 'unpaid' => 'warning',
                        'refunded' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending', 'unpaid' => 'Unpaid',
                        'paid' => 'Paid',
                        'refunded' => 'Refunded',
                        'cancelled' => 'Cancelled',
                        default => ucfirst($state ?? 'N/A'),
                    })
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Order Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'contabo_ok' => 'info',
                        'payment_confirmed' => 'warning',
                        'provision' => 'warning',
                        'pending' => 'gray',
                        'failed', 'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending (Unpaid)',
                        'payment_confirmed' => 'Payment Confirmed (Ready to Deploy)',
                        'provision' => 'Provisioning (Paid)',
                        'contabo_ok' => 'Contabo OK (Ready to Deliver)',
                        'active' => 'Active / Delivered',
                        'failed' => 'Provisioning Failed',
                        'cancelled' => 'Cancelled',
                        default => ucwords(str_replace('_', ' ', $state)),
                    })
                    ->toggleable(),
                TextColumn::make('services.ip_address')
                    ->label('Server IP')
                    ->placeholder('Pending Provisioning')
                    ->copyable()
                    ->copyMessage('IP copied to clipboard')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->columnToggleFormColumns(2)
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending (Unpaid)',
                        'payment_confirmed' => 'Payment Confirmed',
                        'provision' => 'Provisioning (Paid)',
                        'contabo_ok' => 'Contabo OK (Ready)',
                        'active' => 'Active / Delivered',
                        'failed' => 'Provisioning Failed',
                        'cancelled' => 'Cancelled',
                    ]),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Placed From'),
                        DatePicker::make('created_until')->label('Placed Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->filtersFormColumns(2)
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                Action::make('export')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function ($livewire): StreamedResponse {
                        $records = $livewire->getFilteredTableQuery()->with(['user', 'invoice', 'services.package'])->get();
                        $filename = 'orders-export-' . now()->format('Y-m-d_His') . '.csv';

                        return response()->streamDownload(function () use ($records) {
                            $file = fopen('php://output', 'w');
                            fputs($file, "\xEF\xBB\xBF");
                            fputcsv($file, ['Order #', 'Placed At', 'Customer Name', 'Customer Email', 'Packages', 'Invoice #', 'Total Amount', 'Payment Method', 'Payment Status', 'Order Status', 'Server IPs']);

                            foreach ($records as $record) {
                                $packages = $record->services->map(fn ($s) => $s->package?->name)->filter()->implode(', ');
                                $ips = $record->services->map(fn ($s) => $s->ip_address)->filter()->implode(', ');

                                fputcsv($file, [
                                    $record->order_number,
                                    $record->created_at?->toIso8601String(),
                                    $record->user?->name ?? 'N/A',
                                    $record->user?->email ?? 'N/A',
                                    $packages ?: 'N/A',
                                    $record->invoice?->invoice_number ?? 'N/A',
                                    number_format((float) $record->total_amount, 2, '.', ''),
                                    strtoupper($record->invoice?->payment_method ?? 'N/A'),
                                    ucfirst($record->invoice?->status ?? 'Unpaid'),
                                    $record->status,
                                    $ips ?: 'Pending',
                                ]);
                            }
                            fclose($file);
                        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
                    }),
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
