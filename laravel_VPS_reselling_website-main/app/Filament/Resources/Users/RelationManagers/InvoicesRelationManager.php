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

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    protected static ?string $title = 'Invoices';

    protected static string | \BackedEnum | null $icon = 'heroicon-o-document-text';

    public static function getBadge(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): ?string
    {
        $count = $ownerRecord->invoices()->count();
        return $count > 0 ? (string) $count : null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(null)
            ->recordTitleAttribute('invoice_number')
            ->defaultSort('created_at', 'desc')
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
                    ->placeholder('N/A')
                    ->toggleable(),
                TextColumn::make('total')
                    ->label('Total')
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
                    ->toggleable(isToggledHiddenByDefault: true),
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
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('M d, Y')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
                    ->label('Payment Method')
                    ->options([
                        'stripe' => 'Stripe',
                        'crypto' => 'Cryptocurrency',
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
                        $records = $livewire->getFilteredTableQuery()->with(['order'])->get();
                        $filename = 'customer-invoices-' . now()->format('Y-m-d_His') . '.csv';

                        return response()->streamDownload(function () use ($records) {
                            $file = fopen('php://output', 'w');
                            fputs($file, "\xEF\xBB\xBF");
                            fputcsv($file, ['Invoice #', 'Issued At', 'Order #', 'Total Amount', 'Status', 'Paid At', 'Method', 'Due Date']);

                            foreach ($records as $record) {
                                fputcsv($file, [
                                    $record->invoice_number,
                                    $record->created_at?->format('Y-m-d H:i:s'),
                                    $record->order?->order_number ?? 'N/A',
                                    number_format((float) $record->total, 2, '.', ''),
                                    $record->status,
                                    $record->paid_at?->format('Y-m-d H:i:s') ?? '-',
                                    strtoupper($record->payment_method ?? 'N/A'),
                                    $record->due_date ? $record->due_date->format('Y-m-d') : '-',
                                ]);
                            }
                            fclose($file);
                        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
                    }),
            ]);
    }
}
