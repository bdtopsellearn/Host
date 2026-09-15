<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'activityLogs';

    protected static ?string $title = 'Activity Log';

    protected static string | \BackedEnum | null $icon = 'heroicon-o-clock';

    public static function getBadge(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): ?string
    {
        if (!\App\Models\UserActivityLog::tableExists()) {
            return null;
        }

        $count = $ownerRecord->activityLogs()->count();
        return $count > 0 ? (string) $count : null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(null)
            ->recordTitleAttribute('action')
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                if (!\App\Models\UserActivityLog::tableExists()) {
                    return $query->whereRaw('1 = 0');
                }
                return $query->with('user');
            })
            ->emptyStateHeading(fn () => \App\Models\UserActivityLog::tableExists() ? 'No Activity Records' : 'Activity Logs Pending Database Migration')
            ->emptyStateDescription(fn () => \App\Models\UserActivityLog::tableExists() ? 'This customer has no recorded activity yet.' : 'Please run "php artisan migrate" to create the activity logs table.')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime('M d, Y H:i:s')
                    ->sortable()
                    ->weight('bold')
                    ->toggleable(),
                TextColumn::make('action')
                    ->label('Event / Action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'LOGIN', 'LOGOUT' => 'gray',
                        'ORDER_PLACED', 'INVOICE_PAID', 'CREDIT_ADDED', 'PORTAL_RESTORED', 'REGISTERED' => 'success',
                        'PROFILE_UPDATED', 'PASSWORD_CHANGED', 'PASSWORD_RESET', 'SERVER_REBOOTED', 'SERVER_PASSWORD_RESET' => 'warning',
                        'TICKET_OPENED', 'TICKET_REPLIED', 'STAFF_TICKET_REPLY', 'TICKET_REOPENED' => 'info',
                        'CREDIT_DEPOSIT_INITIATED', 'STAFF_CREDIT_ADDED', 'SERVER_STARTED', 'SERVICE_RENEWED' => 'primary',
                        'PORTAL_BLOCKED', 'SERVER_STOPPED', 'SERVER_SHUTDOWN', 'TICKET_CLOSED', 'SERVER_RESCUE_MODE' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->placeholder('-')
                    ->copyable()
                    ->icon('heroicon-o-globe-alt')
                    ->toggleable(),
                TextColumn::make('location')
                    ->label('Location / Origin')
                    ->badge()
                    ->color(fn (?string $state): string => str_contains($state ?? '', 'LAN') || str_contains($state ?? '', 'Local') ? 'gray' : 'info')
                    ->icon('heroicon-o-map-pin')
                    ->toggleable(),
            ])
            ->columnToggleFormColumns(2)
            ->filters([
                SelectFilter::make('action')
                    ->label('Event / Action')
                    ->options([
                        'LOGIN' => 'User Login',
                        'LOGOUT' => 'User Logout',
                        'REGISTERED' => 'Account Registered',
                        'PASSWORD_CHANGED' => 'Password Changed',
                        'PASSWORD_RESET' => 'Password Reset',
                        'PROFILE_UPDATED' => 'Profile Details Updated',
                        'ORDER_PLACED' => 'Order Placed',
                        'PAYMENT_SUBMITTED' => 'Payment Hash Submitted',
                        'INVOICE_PAID' => 'Invoice Paid',
                        'CREDIT_ADDED' => 'Credit Added (Deposit Settled)',
                        'CREDIT_DEPOSIT_INITIATED' => 'Credit Deposit Initiated',
                        'STAFF_CREDIT_ADDED' => 'Staff Added Credit',
                        'SERVICE_RENEWED' => 'Service Renewed',
                        'TICKET_OPENED' => 'Support Ticket Opened',
                        'TICKET_REPLIED' => 'Customer Ticket Reply',
                        'STAFF_TICKET_REPLY' => 'Staff Ticket Reply',
                        'TICKET_CLOSED' => 'Support Ticket Closed',
                        'TICKET_REOPENED' => 'Support Ticket Reopened',
                        'SERVER_STARTED' => 'VPS Powered On',
                        'SERVER_REBOOTED' => 'VPS Rebooted',
                        'SERVER_STOPPED' => 'VPS Forced Power-Off',
                        'SERVER_SHUTDOWN' => 'VPS Graceful Shutdown',
                        'SERVER_PASSWORD_RESET' => 'VPS Root Password Reset',
                        'SERVER_RESCUE_MODE' => 'VPS Rescue Mode',
                        'PORTAL_BLOCKED' => 'Portal Access Blocked',
                        'PORTAL_RESTORED' => 'Portal Access Restored',
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
                    ->modalHeading('Activity Log Details')
                    ->infolist([
                        TextEntry::make('created_at')->label('Timestamp')->dateTime('M d, Y H:i:s'),
                        TextEntry::make('action')
                            ->label('Event / Action')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'LOGIN', 'LOGOUT' => 'gray',
                                'ORDER_PLACED', 'INVOICE_PAID', 'CREDIT_ADDED', 'PORTAL_RESTORED', 'REGISTERED' => 'success',
                                'PROFILE_UPDATED', 'PASSWORD_CHANGED', 'PASSWORD_RESET', 'SERVER_REBOOTED', 'SERVER_PASSWORD_RESET' => 'warning',
                                'TICKET_OPENED', 'TICKET_REPLIED', 'STAFF_TICKET_REPLY', 'TICKET_REOPENED' => 'info',
                                'CREDIT_DEPOSIT_INITIATED', 'STAFF_CREDIT_ADDED', 'SERVER_STARTED', 'SERVICE_RENEWED' => 'primary',
                                'PORTAL_BLOCKED', 'SERVER_STOPPED', 'SERVER_SHUTDOWN', 'TICKET_CLOSED', 'SERVER_RESCUE_MODE' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('ip_address')->label('IP Address')->placeholder('-')->copyable(),
                        TextEntry::make('location')->label('Location / Origin')->badge()->icon('heroicon-o-map-pin'),
                        TextEntry::make('device')->label('Device')->badge(),
                        TextEntry::make('browser')->label('Browser')->badge()->color('info'),
                        TextEntry::make('platform')->label('OS')->badge()->color('gray'),
                        TextEntry::make('description')->label('Description')->columnSpanFull()->prose(),
                        TextEntry::make('user_agent')->label('User-Agent Header')->columnSpanFull()->fontFamily(\Filament\Support\Enums\FontFamily::Mono)->placeholder('-')->copyable(),
                    ]),
            ])
            ->toolbarActions([
                Action::make('export')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function ($livewire): StreamedResponse {
                        $records = $livewire->getFilteredTableQuery()->with('user')->get();
                        $filename = 'customer-activity-logs-' . now()->format('Y-m-d_His') . '.csv';

                        return response()->streamDownload(function () use ($records) {
                            $file = fopen('php://output', 'w');
                            fputs($file, "\xEF\xBB\xBF");
                            fputcsv($file, ['Timestamp', 'Event / Action', 'Description', 'IP Address', 'Location / Origin']);

                            foreach ($records as $record) {
                                fputcsv($file, [
                                    $record->created_at?->format('Y-m-d H:i:s'),
                                    $record->action,
                                    $record->description,
                                    $record->ip_address ?: 'N/A',
                                    $record->location,
                                ]);
                            }
                            fclose($file);
                        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
                    }),
            ]);
    }
}
