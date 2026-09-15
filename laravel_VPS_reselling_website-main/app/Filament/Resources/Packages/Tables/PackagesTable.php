<?php

namespace App\Filament\Resources\Packages\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Package Plan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->toggleable(),
                TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'vps' => 'info',
                        'rdp' => 'warning',
                        default => 'gray',
                    })
                    ->toggleable(),
                TextColumn::make('specs.cores')
                    ->label('Cores')
                    ->placeholder('N/A')
                    ->toggleable(),
                TextColumn::make('specs.memory')
                    ->label('RAM')
                    ->placeholder('N/A')
                    ->toggleable(),
                TextColumn::make('specs.storage')
                    ->label('Storage')
                    ->placeholder('N/A')
                    ->toggleable(),
                TextColumn::make('price_monthly')
                    ->label('Monthly')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('setup_fee')
                    ->label('Setup Fee')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('contabo_product_id')
                    ->label('Contabo ID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
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
                SelectFilter::make('category')
                    ->options([
                        'vps' => 'Cloud VPS',
                        'rdp' => 'Windows RDP',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Publishing Status')
                    ->placeholder('All Plans')
                    ->trueLabel('Active Plans')
                    ->falseLabel('Disabled / Hidden Plans'),
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
                        $filename = 'packages-export-' . now()->format('Y-m-d_His') . '.csv';

                        return response()->streamDownload(function () use ($records) {
                            $file = fopen('php://output', 'w');
                            fputs($file, "\xEF\xBB\xBF");
                            fputcsv($file, [
                                'ID',
                                'Plan Name',
                                'Category',
                                'vCPU Cores',
                                'RAM Memory',
                                'Storage',
                                'Monthly Price ($)',
                                'Setup Fee ($)',
                                'Contabo Product ID',
                                'Is Active',
                                'Created At',
                            ]);

                            foreach ($records as $record) {
                                fputcsv($file, [
                                    $record->id,
                                    $record->name,
                                    strtoupper($record->category),
                                    $record->specs['cores'] ?? 'N/A',
                                    $record->specs['memory'] ?? 'N/A',
                                    $record->specs['storage'] ?? 'N/A',
                                    number_format((float) $record->price_monthly, 2, '.', ''),
                                    number_format((float) ($record->setup_fee ?? 0), 2, '.', ''),
                                    $record->contabo_product_id ?? 'N/A',
                                    $record->is_active ? 'Yes' : 'No',
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
