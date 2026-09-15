<?php

namespace App\Filament\Resources\Coupons\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Promo Code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->toggleable(),
                TextColumn::make('value')
                    ->label('Discount')
                    ->formatStateUsing(fn ($record) => $record->type === 'percent' ? "{$record->value}%" : '$' . number_format((float) $record->value, 2))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('usage_limit')
                    ->label('Limit')
                    ->numeric()
                    ->placeholder('Unlimited')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('used_count')
                    ->label('Redeemed')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('expiry_date')
                    ->label('Expires On')
                    ->date()
                    ->placeholder('Never')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->columnToggleFormColumns(2)
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'fixed' => 'Fixed Discount ($)',
                        'percent' => 'Percentage (%)',
                    ]),
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
                        $records = $livewire->getFilteredTableQuery()->get();
                        $filename = 'coupons-export-' . now()->format('Y-m-d_His') . '.csv';

                        return response()->streamDownload(function () use ($records) {
                            $file = fopen('php://output', 'w');
                            fputs($file, "\xEF\xBB\xBF");
                            fputcsv($file, [
                                'ID',
                                'Promo Code',
                                'Discount Type',
                                'Value',
                                'Usage Limit',
                                'Used Count',
                                'Expiry Date',
                                'Created At',
                            ]);

                            foreach ($records as $record) {
                                fputcsv($file, [
                                    $record->id,
                                    $record->code,
                                    ucfirst($record->type),
                                    $record->value,
                                    $record->usage_limit ?? 'Unlimited',
                                    $record->used_count ?? 0,
                                    $record->expiry_date ? $record->expiry_date->format('Y-m-d') : 'Never',
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
