<?php

namespace App\Filament\Resources\PackageAddons\Tables;

use App\Models\PackageAddon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PackageAddonsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->colors([
                        'primary' => 'os',
                        'success' => 'region',
                        'warning' => 'storage',
                        'info' => 'backup',
                        'danger' => 'network',
                    ])
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('name')
                    ->label('Addon Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->toggleable(),
                TextColumn::make('package.name')
                    ->label('Scope / Target Tier')
                    ->placeholder('All Packages (Global Base)')
                    ->badge()
                    ->color(fn($state) => $state ? 'purple' : 'gray')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('api_identifier')
                    ->label('Contabo API Slug')
                    ->fontFamily('mono')
                    ->copyable()
                    ->placeholder('None')
                    ->toggleable(),
                TextColumn::make('price')
                    ->label('Retail Price')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('is_enabled')
                    ->label('Enabled')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(),
                IconColumn::make('is_out_of_stock')
                    ->label('Sold Out')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('danger')
                    ->falseColor('gray')
                    ->toggleable(),
                TextColumn::make('sort_order')
                    ->label('Order')
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
                        'os' => 'Operating System (OS)',
                        'region' => 'Datacenter Region',
                        'storage' => 'NVMe Storage',
                        'backup' => 'Backups',
                        'network' => 'Private Networking',
                    ]),
                SelectFilter::make('package_id')
                    ->label('Package')
                    ->relationship('package', 'name'),
                TernaryFilter::make('is_global')
                    ->label('Global Base vs Override'),
                TernaryFilter::make('is_out_of_stock')
                    ->label('Out of Stock Status'),
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
                        $records = $livewire->getFilteredTableQuery()->with(['package'])->get();
                        $filename = 'package-addons-export-' . now()->format('Y-m-d_His') . '.csv';

                        return response()->streamDownload(function () use ($records) {
                            $file = fopen('php://output', 'w');
                            fputs($file, "\xEF\xBB\xBF");
                            fputcsv($file, [
                                'ID',
                                'Category Type',
                                'Addon Name',
                                'Scope / Tier',
                                'Contabo API Slug',
                                'Price ($)',
                                'Enabled',
                                'Sold Out',
                                'Sort Order',
                            ]);

                            foreach ($records as $record) {
                                fputcsv($file, [
                                    $record->id,
                                    strtoupper($record->type),
                                    $record->name,
                                    $record->package?->name ?? 'All Packages (Global)',
                                    $record->api_identifier ?: 'None',
                                    number_format((float) $record->price, 2, '.', ''),
                                    $record->is_enabled ? 'Yes' : 'No',
                                    $record->is_out_of_stock ? 'Yes' : 'No',
                                    $record->sort_order,
                                ]);
                            }
                            fclose($file);
                        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
                    }),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultGroup('type')
            ->groups([
                Group::make('type')
                    ->label('Category')
                    ->getTitleFromRecordUsing(fn (PackageAddon $record): string => match ($record->type) {
                        'os' => '🐧 Operating Systems (OS)',
                        'region' => '🌐 Datacenter Regions',
                        'storage' => '💾 Primary NVMe Storage Tiers',
                        'backup' => '🛡️ Data Protection & Backups',
                        'network' => '🔒 Private Networking VPC',
                        default => '⚡ Additional Features & Addons',
                    })
                    ->collapsible(),
            ]);
    }
}
