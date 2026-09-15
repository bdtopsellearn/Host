<?php

namespace App\Filament\Resources\Services\Tables;

use App\Models\Service;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->placeholder('N/A')
                    ->copyable()
                    ->copyMessage('Order # copied')
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Service $record) => $record->user->email ?? '')
                    ->toggleable(),
                TextColumn::make('package.name')
                    ->label('Package')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('ip_address')
                    ->label('Server IP')
                    ->searchable()
                    ->copyable()
                    ->placeholder('Not Assigned')
                    ->weight('bold')
                    ->toggleable(),
                TextColumn::make('contabo_instance_id')
                    ->label('Contabo ID')
                    ->searchable()
                    ->placeholder('N/A')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'contabo_ok' => 'info',
                        'provisioning' => 'warning',
                        'suspended' => 'danger',
                        'terminated', 'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'provisioning' => 'Provisioning',
                        'contabo_ok' => 'Contabo OK',
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'terminated' => 'Terminated',
                        'cancelled' => 'Cancelled',
                        default => ucfirst($state),
                    })
                    ->toggleable(),
                TextColumn::make('recurring_amount')
                    ->label('Renewal Rate')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('billing_cycle')
                    ->label('Cycle')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->toggleable(),
                TextColumn::make('next_due_date')
                    ->label('Next Due Date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->columnToggleFormColumns(2)
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'contabo_ok' => 'Contabo OK',
                        'provisioning' => 'Provisioning',
                        'suspended' => 'Suspended',
                        'terminated' => 'Terminated',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('billing_cycle')
                    ->options([
                        'monthly' => 'Monthly',
                        'annually' => 'Annually (1 Year)',
                        'biennially' => 'Biennially (2 Years)',
                    ]),
                SelectFilter::make('package_id')
                    ->label('Package Plan')
                    ->relationship('package', 'name'),
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
                        $records = $livewire->getFilteredTableQuery()->with(['user', 'order', 'package'])->get();
                        $filename = 'active-orders-export-' . now()->format('Y-m-d_His') . '.csv';

                        return response()->streamDownload(function () use ($records) {
                            $file = fopen('php://output', 'w');
                            fputs($file, "\xEF\xBB\xBF");
                            fputcsv($file, [
                                'Order #',
                                'Customer Name',
                                'Customer Email',
                                'Package',
                                'Server IP',
                                'Contabo Instance ID',
                                'Status',
                                'Renewal Rate ($)',
                                'Billing Cycle',
                                'Next Due Date',
                                'Created At',
                            ]);

                            foreach ($records as $record) {
                                fputcsv($file, [
                                    $record->order?->order_number ?? 'N/A',
                                    $record->user?->name ?? 'N/A',
                                    $record->user?->email ?? 'N/A',
                                    $record->package?->name ?? 'N/A',
                                    $record->ip_address ?: 'Not Assigned',
                                    $record->contabo_instance_id ?: 'N/A',
                                    $record->status,
                                    number_format((float) $record->recurring_amount, 2, '.', ''),
                                    ucfirst($record->billing_cycle ?? 'monthly'),
                                    $record->next_due_date ? $record->next_due_date->format('Y-m-d') : 'N/A',
                                    $record->created_at?->toIso8601String(),
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
