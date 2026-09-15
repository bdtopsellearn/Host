<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use App\Models\UserActivityLog;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CreditsRelationManager extends RelationManager
{
    protected static string $relationship = 'credits';

    protected static ?string $title = 'Credits';

    protected static string | \BackedEnum | null $icon = 'heroicon-o-banknotes';

    public static function getBadge(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): ?string
    {
        $count = $ownerRecord->credits()->count();
        return $count > 0 ? (string) $count : null;
    }

    public function table(Table $table): Table
    {
        $balance = number_format((float) $this->ownerRecord->credit_balance, 2);

        return $table
            ->heading("Available Balance: \${$balance} USD")
            ->description('Pre-funded wallet funds automatically applied to cloud VPS renewals and pending invoices.')
            ->recordTitleAttribute('invoice_number')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Deposit Ref #')
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->weight('bold')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Initiated Date')
                    ->dateTime('M d, Y H:i:s')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('total')
                    ->label('Credit Amount')
                    ->money('USD')
                    ->weight('bold')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('payment_method')
                    ->label('Payment Source')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'crypto' => 'info',
                        'admin_credit', 'manual_credit' => 'primary',
                        'wire_transfer' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(function (Invoice $record): string {
                        $method = strtoupper($record->payment_method ?? 'CRYPTO');
                        if ($record->crypto_network) {
                            $net = match ($record->crypto_network) {
                                'usdt_trc20' => 'USDT (Tron)',
                                'usdc_polygon' => 'USDC (Polygon)',
                                'usdt_polygon' => 'USDT (Polygon)',
                                default => strtoupper($record->crypto_network),
                            };
                            return "{$method} - {$net}";
                        }
                        if (in_array($record->payment_method, ['admin_credit', 'manual_credit'])) {
                            return 'STAFF CREDIT';
                        }
                        return $method;
                    })
                    ->toggleable(),

                TextColumn::make('status')
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
                        'paid' => 'Paid & Credited',
                        'pending', 'unpaid' => 'Awaiting Payment',
                        default => ucfirst($state),
                    })
                    ->toggleable(),

                TextColumn::make('crypto_txid')
                    ->label('TxID / Reference')
                    ->placeholder('N/A')
                    ->copyable()
                    ->limit(16)
                    ->url(fn (Invoice $record) => !empty($record->crypto_txid)
                        ? ((str_starts_with($record->crypto_txid, '0x') || str_contains($record->crypto_network ?? '', 'polygon'))
                            ? "https://polygonscan.com/tx/{$record->crypto_txid}"
                            : "https://tronscan.org/#/transaction/{$record->crypto_txid}")
                        : null, true)
                    ->icon(fn (Invoice $record) => !empty($record->crypto_txid) ? 'heroicon-m-arrow-top-right-on-square' : null)
                    ->iconPosition('after')
                    ->toggleable(),

                TextColumn::make('paid_at')
                    ->label('Settled Date')
                    ->dateTime('M d, Y H:i:s')
                    ->placeholder('Pending Settlement')
                    ->toggleable(),
            ])
            ->columnToggleFormColumns(2)
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'paid' => 'Paid & Credited',
                        'unpaid' => 'Awaiting Payment',
                        'cancelled' => 'Cancelled',
                    ]),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('From Date'),
                        DatePicker::make('created_until')->label('Until Date'),
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
                Action::make('add_credit')
                    ->label('Add / Adjust Credit')
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary')
                    ->modalHeading('Add Account Credit to Customer')
                    ->modalDescription('Manually credit or adjust this customer\'s account balance. Credits are immediately applied to pending invoices and renewals.')
                    ->modalSubmitActionLabel('Add Funds to Balance')
                    ->form([
                        TextInput::make('amount')
                            ->label('Credit Amount (USD)')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->minValue(1)
                            ->maxValue(50000)
                            ->placeholder('50.00'),

                        Select::make('payment_method')
                            ->label('Funding Source')
                            ->options([
                                'manual_credit' => 'Manual Staff Credit / Adjustment',
                                'wire_transfer' => 'Direct Bank Wire / Transfer',
                                'crypto' => 'Manual Crypto Settlement (Off-chain)',
                                'bonus' => 'Customer Goodwill / Loyalty Credit',
                            ])
                            ->default('manual_credit')
                            ->required(),

                        Textarea::make('note')
                            ->label('Staff Note / Reason')
                            ->placeholder('e.g. Bank wire deposit received, SLA compensation, or goodwill top-up.')
                            ->rows(3),

                        Select::make('status')
                            ->label('Deposit Status')
                            ->options([
                                'paid' => 'Paid & Credited Immediately',
                                'unpaid' => 'Unpaid (Awaiting Customer Payment)',
                            ])
                            ->default('paid')
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $amount = round((float) $data['amount'], 2);

                        $invoice = Invoice::create([
                            'user_id' => $this->ownerRecord->id,
                            'order_id' => null,
                            'service_id' => null,
                            'amount' => $amount,
                            'tax' => 0.00,
                            'total' => $amount,
                            'status' => $data['status'],
                            'payment_method' => $data['payment_method'],
                            'due_date' => now()->toDateString(),
                            'paid_at' => $data['status'] === 'paid' ? now() : null,
                        ]);

                        $adminName = auth('admin')->user()?->name ?? 'Staff';
                        $note = !empty($data['note']) ? $data['note'] : 'Staff manual credit';

                        UserActivityLog::create([
                            'user_id' => $this->ownerRecord->id,
                            'action' => 'STAFF_CREDIT_ADDED',
                            'description' => "Admin {$adminName} added \${$amount} credit (#{$invoice->invoice_number}). Reason: {$note}",
                            'ip_address' => request()->ip(),
                        ]);

                        $newBalance = number_format($this->ownerRecord->fresh()->credit_balance, 2);

                        Notification::make()
                            ->title('Credit Applied Successfully')
                            ->body("\${$amount} has been added to {$this->ownerRecord->name}. New balance: \${$newBalance}")
                            ->success()
                            ->send();
                    }),

                Action::make('export')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function ($livewire): StreamedResponse {
                        $records = $livewire->getFilteredTableQuery()->get();
                        $filename = 'customer-credits-' . now()->format('Y-m-d_His') . '.csv';

                        return response()->streamDownload(function () use ($records) {
                            $file = fopen('php://output', 'w');
                            fputs($file, "\xEF\xBB\xBF");
                            fputcsv($file, ['Deposit Ref #', 'Date & Time', 'Amount (USD)', 'Source', 'Status', 'TxID', 'Settled At']);

                            foreach ($records as $record) {
                                fputcsv($file, [
                                    $record->invoice_number,
                                    $record->created_at?->format('Y-m-d H:i:s') ?? '-',
                                    number_format((float) $record->total, 2, '.', ''),
                                    strtoupper($record->payment_method ?? 'N/A'),
                                    $record->status,
                                    $record->crypto_txid ?? 'N/A',
                                    $record->paid_at?->format('Y-m-d H:i:s') ?? 'Pending',
                                ]);
                            }
                            fclose($file);
                        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
                    }),
            ]);
    }
}
