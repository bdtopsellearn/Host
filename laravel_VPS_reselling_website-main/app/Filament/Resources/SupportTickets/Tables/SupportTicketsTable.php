<?php

namespace App\Filament\Resources\SupportTickets\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupportTicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Ticket #')
                    ->sortable()
                    ->weight('bold')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Opened At')
                    ->dateTime('M d, Y H:i:s')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Guest / Unknown')
                    ->description(fn ($record) => $record->user?->email ?? '')
                    ->toggleable(),
                TextColumn::make('department')
                    ->label('Department')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->toggleable(),
                TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('priority')
                    ->label('Priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        'low' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'warning',
                        'in_progress' => 'info',
                        'answered' => 'success',
                        'closed' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in_progress' => 'In Progress',
                        default => ucfirst($state),
                    })
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Last Activity')
                    ->dateTime('M d, Y H:i:s')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->columnToggleFormColumns(2)
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'answered' => 'Answered',
                        'closed' => 'Closed',
                    ]),
                SelectFilter::make('priority')
                    ->options([
                        'urgent' => 'Urgent',
                        'high' => 'High',
                        'medium' => 'Medium',
                        'low' => 'Low',
                    ]),
                SelectFilter::make('department')
                    ->options([
                        'support' => 'Technical Support',
                        'billing' => 'Billing & Payments',
                        'sales' => 'Sales',
                        'general' => 'General Inquiry',
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
                        $records = $livewire->getFilteredTableQuery()->with(['user'])->get();
                        $filename = 'support-tickets-export-' . now()->format('Y-m-d_His') . '.csv';

                        return response()->streamDownload(function () use ($records) {
                            $file = fopen('php://output', 'w');
                            fputs($file, "\xEF\xBB\xBF");
                            fputcsv($file, [
                                'Ticket #',
                                'Opened At',
                                'Customer Name',
                                'Customer Email',
                                'Department',
                                'Subject',
                                'Priority',
                                'Status',
                                'Last Updated',
                            ]);

                            foreach ($records as $record) {
                                fputcsv($file, [
                                    $record->id,
                                    $record->created_at?->toIso8601String(),
                                    $record->user?->name ?? 'N/A',
                                    $record->user?->email ?? 'N/A',
                                    ucfirst($record->department ?? 'General'),
                                    $record->subject,
                                    ucfirst($record->priority ?? 'Medium'),
                                    ucfirst($record->status ?? 'Open'),
                                    $record->updated_at?->toIso8601String(),
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
