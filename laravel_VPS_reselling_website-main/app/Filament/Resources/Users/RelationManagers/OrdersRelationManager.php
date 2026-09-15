<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
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

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $title = 'Orders';

    protected static string | \BackedEnum | null $icon = 'heroicon-o-shopping-bag';

    public static function getBadge(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): ?string
    {
        $count = $ownerRecord->orders()->count();
        return $count > 0 ? (string) $count : null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(null)
            ->recordTitleAttribute('order_number')
            ->defaultSort('created_at', 'desc')
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
                TextColumn::make('services.package.name')
                    ->label('Package')
                    ->badge()
                    ->color('primary')
                    ->placeholder('N/A')
                    ->toggleable(),
                TextColumn::make('invoice.invoice_number')
                    ->label('Invoice #')
                    ->placeholder('N/A')
                    ->toggleable(),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('invoice.payment_method')
                    ->label('Method')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => strtoupper($state ?? 'N/A'))
                    ->toggleable(),
                TextColumn::make('invoice.status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending', 'unpaid' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => ucfirst($state ?? 'Unpaid'))
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Order Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'contabo_ok' => 'info',
                        'payment_confirmed', 'provision' => 'warning',
                        'pending' => 'gray',
                        'failed', 'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->toggleable(),
                TextColumn::make('services.ip_address')
                    ->label('Server IP')
                    ->placeholder('Pending Provisioning')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->columnToggleFormColumns(2)
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'payment_confirmed' => 'Payment Confirmed',
                        'provision' => 'Provisioning',
                        'contabo_ok' => 'Contabo OK',
                        'active' => 'Active',
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
                ViewAction::make()
                    ->url(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record])),
            ])
            ->toolbarActions([
                Action::make('export')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function ($livewire): StreamedResponse {
                        $records = $livewire->getFilteredTableQuery()->with(['invoice', 'services.package'])->get();
                        $filename = 'customer-orders-' . now()->format('Y-m-d_His') . '.csv';

                        return response()->streamDownload(function () use ($records) {
                            $file = fopen('php://output', 'w');
                            fputs($file, "\xEF\xBB\xBF");
                            fputcsv($file, ['Order #', 'Placed At', 'Package', 'Invoice #', 'Total Amount', 'Payment Method', 'Payment Status', 'Order Status']);

                            foreach ($records as $record) {
                                fputcsv($file, [
                                    $record->order_number,
                                    $record->created_at?->format('Y-m-d H:i:s'),
                                    $record->services->map(fn ($s) => $s->package?->name)->filter()->implode(', ') ?: 'N/A',
                                    $record->invoice?->invoice_number ?? 'N/A',
                                    number_format((float) $record->total_amount, 2, '.', ''),
                                    strtoupper($record->invoice?->payment_method ?? 'N/A'),
                                    ucfirst($record->invoice?->status ?? 'Unpaid'),
                                    $record->status,
                                ]);
                            }
                            fclose($file);
                        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
                    }),
            ]);
    }
}
