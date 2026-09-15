<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Services\ServiceResource;
use App\Models\Service;
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

class ServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'services';

    protected static ?string $title = 'VPS Services';

    protected static string | \BackedEnum | null $icon = 'heroicon-o-server-stack';

    public static function getBadge(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): ?string
    {
        $count = $ownerRecord->services()->count();
        return $count > 0 ? (string) $count : null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(null)
            ->recordTitleAttribute('server_name')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('formatted_hostname')
                    ->label('Server / Hostname')
                    ->weight('bold')
                    ->copyable()
                    ->searchable(['server_name'])
                    ->toggleable(),
                TextColumn::make('package.name')
                    ->label('Package')
                    ->badge()
                    ->color('primary')
                    ->toggleable(),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->placeholder('Pending Assignment')
                    ->copyable()
                    ->icon('heroicon-o-globe-alt')
                    ->color(fn ($state) => empty($state) || $state === 'Pending IP' ? 'warning' : 'success')
                    ->toggleable(),
                TextColumn::make('region')
                    ->label('Region')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => strtoupper($state ?? 'EU'))
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active', 'contabo_ok', 'provisioned' => 'success',
                        'provisioning', 'awaiting_provisioning', 'ready_for_provisioning' => 'warning',
                        'suspended', 'cancelled', 'terminated' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->toggleable(),
                TextColumn::make('next_due_date')
                    ->label('Next Due Date')
                    ->date('M d, Y')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('billing_cycle')
                    ->label('Billing Cycle')
                    ->formatStateUsing(fn (?string $state): string => ucfirst($state ?? 'Monthly'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('recurring_amount')
                    ->label('Price')
                    ->money('USD')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->columnToggleFormColumns(2)
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'provisioning' => 'Provisioning',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('package_id')
                    ->label('Package Plan')
                    ->relationship('package', 'name'),
                Filter::make('next_due_date')
                    ->form([
                        DatePicker::make('due_from')->label('Due From'),
                        DatePicker::make('due_until')->label('Due Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['due_from'], fn ($q, $date) => $q->whereDate('next_due_date', '>=', $date))
                            ->when($data['due_until'], fn ($q, $date) => $q->whereDate('next_due_date', '<=', $date));
                    }),
            ])
            ->filtersFormColumns(2)
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Service $record): string => ServiceResource::getUrl('view', ['record' => $record])),
            ])
            ->toolbarActions([
                Action::make('export')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function ($livewire): StreamedResponse {
                        $records = $livewire->getFilteredTableQuery()->with(['package'])->get();
                        $filename = 'customer-services-' . now()->format('Y-m-d_His') . '.csv';

                        return response()->streamDownload(function () use ($records) {
                            $file = fopen('php://output', 'w');
                            fputs($file, "\xEF\xBB\xBF");
                            fputcsv($file, ['Hostname', 'Package', 'IP Address', 'Region', 'Status', 'Renewal Rate', 'Next Due Date']);

                            foreach ($records as $record) {
                                fputcsv($file, [
                                    $record->formatted_hostname,
                                    $record->package?->name ?? 'N/A',
                                    $record->ip_address ?: 'Not Assigned',
                                    strtoupper($record->region ?? 'EU'),
                                    $record->status,
                                    number_format((float) $record->recurring_amount, 2, '.', ''),
                                    $record->next_due_date ? $record->next_due_date->format('Y-m-d') : 'N/A',
                                ]);
                            }
                            fclose($file);
                        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
                    }),
            ]);
    }
}
