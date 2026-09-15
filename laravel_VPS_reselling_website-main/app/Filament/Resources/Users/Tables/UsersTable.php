<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount([
                'services as active_services_count' => fn ($q) => $q->whereIn('status', User::ACTIVE_SERVICE_STATUSES),
                'services as suspended_services_count' => fn ($q) => $q->whereIn('status', User::SUSPENDED_SERVICE_STATUSES),
            ]))
            ->columns([
                TextColumn::make('name')
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->toggleable(),
                TextColumn::make('customer_status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (User $record): string => $record->customer_status)
                    ->color(function (string $state): string {
                        if (str_starts_with($state, 'Active')) {
                            return 'success'; // Green
                        }
                        if (str_starts_with($state, 'Suspended')) {
                            return 'warning'; // Amber / Yellow
                        }
                        return 'gray'; // Neutral Slate
                    })
                    ->toggleable(),
                TextColumn::make('email')
                    ->label('Email Address')
                    ->copyable()
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('company_name')
                    ->label('Company')
                    ->placeholder('Personal')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->placeholder('N/A')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('country')
                    ->label('Country')
                    ->badge()
                    ->color('info')
                    ->placeholder('N/A')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Registered')
                    ->date('M j, Y')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->columnToggleFormColumns(2)
            ->filters([
                SelectFilter::make('customer_status')
                    ->label('Customer Status')
                    ->options([
                        'active' => 'Active (Has Active VPS)',
                        'suspended' => 'Suspended (Locked VPS)',
                        'inactive' => 'Inactive (0 Live/Locked VPS)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'active' => $query->activeCustomer(),
                            'suspended' => $query->suspendedCustomer(),
                            'inactive' => $query->inactiveCustomer(),
                            default => $query,
                        };
                    }),
                TernaryFilter::make('email_verified_at')
                    ->label('Email Verification')
                    ->placeholder('All Users')
                    ->trueLabel('Verified Accounts')
                    ->falseLabel('Unverified Accounts')
                    ->nullable(),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('registered_from')->label('Registered From'),
                        DatePicker::make('registered_until')->label('Registered Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['registered_from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['registered_until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->filtersFormColumns(3)
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                Action::make('export')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function ($livewire): StreamedResponse {
                        $records = $livewire->getFilteredTableQuery()->withCount([
                            'services as active_services_count' => fn ($q) => $q->whereIn('status', User::ACTIVE_SERVICE_STATUSES),
                            'services as suspended_services_count' => fn ($q) => $q->whereIn('status', User::SUSPENDED_SERVICE_STATUSES),
                        ])->get();
                        $filename = 'users-export-' . now()->format('Y-m-d_His') . '.csv';

                        return response()->streamDownload(function () use ($records) {
                            $file = fopen('php://output', 'w');
                            fputs($file, "\xEF\xBB\xBF");
                            fputcsv($file, [
                                'User ID',
                                'Full Name',
                                'Customer Status',
                                'Active VPS Count',
                                'Email Address',
                                'Company',
                                'Phone',
                                'Country',
                                'Email Verified At',
                                'Registered At',
                            ]);

                            foreach ($records as $record) {
                                $active = $record->active_services_count ?? 0;
                                $suspended = $record->suspended_services_count ?? 0;
                                $status = ($active > 0) ? "Active ({$active})" : (($suspended > 0) ? "Suspended ({$suspended})" : 'Inactive');

                                fputcsv($file, [
                                    $record->id,
                                    $record->name,
                                    $status,
                                    $active,
                                    $record->email,
                                    $record->company_name ?: 'Personal',
                                    $record->phone ?: 'N/A',
                                    $record->country ?: 'N/A',
                                    $record->email_verified_at ? $record->email_verified_at->toIso8601String() : 'Unverified',
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
