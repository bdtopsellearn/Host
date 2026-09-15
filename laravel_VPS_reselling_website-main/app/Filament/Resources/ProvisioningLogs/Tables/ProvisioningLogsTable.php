<?php

namespace App\Filament\Resources\ProvisioningLogs\Tables;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProvisioningLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime()
                    ->sortable()
                    ->weight('bold')
                    ->toggleable(),
                TextColumn::make('action')
                    ->label('API Action')
                    ->searchable()
                    ->badge()
                    ->color('primary')
                    ->toggleable(),
                TextColumn::make('service.server_name')
                    ->label('Service')
                    ->searchable()
                    ->placeholder('N/A')
                    ->toggleable(),
                IconColumn::make('is_success')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->columnToggleFormColumns(2)
            ->filters([
                TernaryFilter::make('is_success')
                    ->label('API Status')
                    ->placeholder('All Logs')
                    ->trueLabel('Successful Requests')
                    ->falseLabel('Failed Requests / Errors'),
                SelectFilter::make('action')
                    ->label('API Action Type'),
            ])
            ->filtersFormColumns(2)
            ->recordActions([
                ViewAction::make()
                    ->label('View Details')
                    ->modalWidth('7xl'),
            ])
            ->toolbarActions([
                Action::make('export')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function ($livewire): StreamedResponse {
                        $records = $livewire->getFilteredTableQuery()->with(['service'])->get();
                        $filename = 'provisioning-logs-export-' . now()->format('Y-m-d_His') . '.csv';

                        return response()->streamDownload(function () use ($records) {
                            $file = fopen('php://output', 'w');
                            fputs($file, "\xEF\xBB\xBF");
                            fputcsv($file, [
                                'ID',
                                'Timestamp',
                                'Action',
                                'Service Name',
                                'Success',
                                'HTTP Status Code',
                            ]);

                            foreach ($records as $record) {
                                fputcsv($file, [
                                    $record->id,
                                    $record->created_at?->toIso8601String(),
                                    $record->action,
                                    $record->service?->server_name ?? 'N/A',
                                    $record->is_success ? 'Success' : 'Failed',
                                    $record->response_status_code ?? 'N/A',
                                ]);
                            }
                            fclose($file);
                        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
                    }),
            ]);
    }
}
