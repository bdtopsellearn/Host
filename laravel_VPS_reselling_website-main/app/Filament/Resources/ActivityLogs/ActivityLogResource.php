<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Models\User;
use App\Models\UserActivityLog;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogResource extends Resource
{
    protected static ?string $model = UserActivityLog::class;

    protected static ?string $navigationLabel = 'Activity Logs';

    protected static string|\UnitEnum|null $navigationGroup = 'System Settings';

    protected static ?int $navigationSort = 10;

    protected static ?string $modelLabel = 'Activity Log';

    protected static ?string $pluralModelLabel = 'Activity Logs';

    protected static string|BackedEnum|null $navigationIcon = null;

    public static function getNavigationBadge(): ?string
    {
        if (!UserActivityLog::tableExists()) {
            return null;
        }

        $todayCount = UserActivityLog::whereDate('created_at', today())->count();
        return $todayCount > 0 ? (string) $todayCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                if (!UserActivityLog::tableExists()) {
                    return $query->whereRaw('1 = 0');
                }
                return $query->with(['user', 'admin']);
            })
            ->emptyStateHeading(fn () => UserActivityLog::tableExists() ? 'No Activity Logs Found' : 'Activity Logs Pending Database Migration')
            ->emptyStateDescription(fn () => UserActivityLog::tableExists() ? 'No user or administrative events have been recorded yet.' : 'The user_activity_logs table has not been created yet. Run "php artisan migrate" to enable live activity tracking.')
            ->emptyStateIcon(Heroicon::OutlinedFingerPrint)
            ->columns([
                // 1. Primary Identifiers (Visible by Default)
                TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime('M d, Y H:i:s')
                    ->description(fn (UserActivityLog $record): string => $record->created_at?->diffForHumans() ?? '')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->toggleable(),

                TextColumn::make('actor_name')
                    ->label('Actor / Account')
                    ->description(fn (UserActivityLog $record): string => $record->actor_email)
                    ->icon(fn (UserActivityLog $record): string => match ($record->actor_type) {
                        'admin' => 'heroicon-o-shield-check',
                        'user' => 'heroicon-o-user',
                        default => 'heroicon-o-cpu-chip',
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                            ->orWhereHas('admin', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
                    })
                    ->toggleable(),

                TextColumn::make('actor_type')
                    ->label('Role')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'admin' => 'warning',
                        'user' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'admin' => 'Admin Staff',
                        'user' => 'Customer',
                        default => 'System',
                    })
                    ->toggleable(),

                TextColumn::make('action')
                    ->label('Event / Action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'LOGIN', 'LOGOUT' => 'gray',
                        'ORDER_PLACED', 'INVOICE_PAID', 'CREDIT_ADDED', 'PORTAL_RESTORED', 'REGISTERED' => 'success',
                        'PROFILE_UPDATED', 'PASSWORD_CHANGED', 'PASSWORD_RESET', 'SERVER_REBOOTED', 'SERVER_PASSWORD_RESET' => 'warning',
                        'TICKET_OPENED', 'TICKET_REPLIED', 'STAFF_TICKET_REPLY', 'TICKET_REOPENED', 'ADMIN_LOGIN' => 'info',
                        'CREDIT_DEPOSIT_INITIATED', 'STAFF_CREDIT_ADDED', 'SERVER_STARTED', 'SERVICE_RENEWED' => 'primary',
                        'PORTAL_BLOCKED', 'SERVER_STOPPED', 'SERVER_SHUTDOWN', 'TICKET_CLOSED', 'SERVER_RESCUE_MODE', 'FAILED_LOGIN', 'ADMIN_LOGOUT' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->placeholder('-')
                    ->copyable()
                    ->icon('heroicon-o-globe-alt')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('location')
                    ->label('Location / Country')
                    ->badge()
                    ->color(fn (?string $state): string => str_contains($state ?? '', 'LAN') || str_contains($state ?? '', 'Local') ? 'gray' : 'info')
                    ->icon('heroicon-o-map-pin')
                    ->toggleable(),

                // 2. Auxiliary & Forensic Telemetry (Hidden by Default)
                TextColumn::make('device')
                    ->label('Device')
                    ->badge()
                    ->color('gray')
                    ->icon(fn (?string $state): string => match (strtolower($state ?? '')) {
                        'mobile' => 'heroicon-o-device-phone-mobile',
                        'tablet' => 'heroicon-o-device-tablet',
                        'api / bot', 'system / cli' => 'heroicon-o-command-line',
                        default => 'heroicon-o-computer-desktop',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('browser')
                    ->label('Browser')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('platform')
                    ->label('Operating System')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('request_method')
                    ->label('Method')
                    ->badge()
                    ->color(fn (?string $state): string => match (strtoupper($state ?? '')) {
                        'POST' => 'success',
                        'PUT', 'PATCH' => 'warning',
                        'DELETE' => 'danger',
                        default => 'info',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('request_url')
                    ->label('Endpoint / URL')
                    ->wrap()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->columnToggleFormColumns(2)
            ->filters([
                SelectFilter::make('actor_type')
                    ->label('Actor Category')
                    ->options([
                        'user' => 'Customer Activities',
                        'admin' => 'Administrator / Staff Activities',
                        'system' => 'System / Automated Events',
                    ]),

                SelectFilter::make('event_category')
                    ->label('Event Category')
                    ->options([
                        'auth' => 'Authentication & Access',
                        'billing' => 'Billing, Orders & Credits',
                        'vps' => 'Cloud VPS Operations',
                        'tickets' => 'Support Helpdesk',
                        'security' => 'Account & Security Settings',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'auth' => $query->whereIn('action', ['LOGIN', 'LOGOUT', 'ADMIN_LOGIN', 'ADMIN_LOGOUT', 'FAILED_LOGIN', 'REGISTERED', 'PASSWORD_RESET', 'PASSWORD_CHANGED']),
                            'billing' => $query->whereIn('action', ['ORDER_PLACED', 'PAYMENT_SUBMITTED', 'INVOICE_PAID', 'CREDIT_ADDED', 'CREDIT_DEPOSIT_INITIATED', 'STAFF_CREDIT_ADDED', 'SERVICE_RENEWED']),
                            'vps' => $query->whereIn('action', ['SERVER_STARTED', 'SERVER_REBOOTED', 'SERVER_STOPPED', 'SERVER_SHUTDOWN', 'SERVER_PASSWORD_RESET', 'SERVER_RESCUE_MODE']),
                            'tickets' => $query->whereIn('action', ['TICKET_OPENED', 'TICKET_REPLIED', 'STAFF_TICKET_REPLY', 'TICKET_CLOSED', 'TICKET_REOPENED']),
                            'security' => $query->whereIn('action', ['PORTAL_BLOCKED', 'PORTAL_RESTORED', 'PROFILE_UPDATED']),
                            default => $query,
                        };
                    }),

                SelectFilter::make('action')
                    ->label('Specific Action')
                    ->options([
                        'LOGIN' => 'User Login',
                        'LOGOUT' => 'User Logout',
                        'ADMIN_LOGIN' => 'Admin Login',
                        'ADMIN_LOGOUT' => 'Admin Logout',
                        'FAILED_LOGIN' => 'Failed Login Attempt',
                        'REGISTERED' => 'Account Registered',
                        'PASSWORD_CHANGED' => 'Password Changed',
                        'PASSWORD_RESET' => 'Password Reset',
                        'PROFILE_UPDATED' => 'Profile Updated',
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

                SelectFilter::make('user_id')
                    ->label('Customer Account')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

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
                    ->modalHeading('Activity & Telemetry Forensics Inspector')
                    ->modalWidth(Width::FiveExtraLarge)
                    ->infolist([
                        Grid::make(3)->schema([
                            // SECTION 1: Event Identity
                            Section::make('Event Execution & Narrative')
                                ->icon('heroicon-o-sparkles')
                                ->columnSpan(2)
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('action')
                                            ->label('Action Event')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'LOGIN', 'LOGOUT' => 'gray',
                                                'ORDER_PLACED', 'INVOICE_PAID', 'CREDIT_ADDED', 'PORTAL_RESTORED', 'REGISTERED' => 'success',
                                                'PROFILE_UPDATED', 'PASSWORD_CHANGED', 'PASSWORD_RESET', 'SERVER_REBOOTED', 'SERVER_PASSWORD_RESET' => 'warning',
                                                'TICKET_OPENED', 'TICKET_REPLIED', 'STAFF_TICKET_REPLY', 'TICKET_REOPENED', 'ADMIN_LOGIN' => 'info',
                                                'CREDIT_DEPOSIT_INITIATED', 'STAFF_CREDIT_ADDED', 'SERVER_STARTED', 'SERVICE_RENEWED' => 'primary',
                                                'PORTAL_BLOCKED', 'SERVER_STOPPED', 'SERVER_SHUTDOWN', 'TICKET_CLOSED', 'SERVER_RESCUE_MODE', 'FAILED_LOGIN', 'ADMIN_LOGOUT' => 'danger',
                                                default => 'gray',
                                            }),
                                        TextEntry::make('event_category')
                                            ->label('Category')
                                            ->badge()
                                            ->color('gray'),
                                        TextEntry::make('created_at')
                                            ->label('Execution Time')
                                            ->dateTime('M d, Y H:i:s T')
                                            ->helperText(fn (UserActivityLog $record): string => $record->created_at?->diffForHumans() ?? ''),
                                    ]),
                                    TextEntry::make('description')
                                        ->label('Event Narrative & Context')
                                        ->columnSpanFull()
                                        ->prose(),
                                ]),

                            // SECTION 2: Actor Identity Card
                            Section::make('Actor Identity')
                                ->icon('heroicon-o-user')
                                ->columnSpan(1)
                                ->schema([
                                    TextEntry::make('actor_role')
                                        ->label('Role Classification')
                                        ->badge()
                                        ->color(fn (UserActivityLog $record) => $record->actor_badge_color),
                                    TextEntry::make('actor_name')
                                        ->label('Name / Identity')
                                        ->weight(FontWeight::Bold),
                                    TextEntry::make('actor_email')
                                        ->label('Email')
                                        ->copyable(),
                                    TextEntry::make('user.credit_balance')
                                        ->label('Credit Balance')
                                        ->money('USD')
                                        ->visible(fn (UserActivityLog $record) => $record->user_id !== null),
                                ]),
                        ]),

                        Grid::make(2)->schema([
                            // SECTION 3: Geolocation & Network
                            Section::make('Network & Geolocation Forensics')
                                ->icon('heroicon-o-globe-americas')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('ip_address')
                                            ->label('Client IP Address')
                                            ->copyable()
                                            ->icon('heroicon-o-globe-alt'),
                                        TextEntry::make('location')
                                            ->label('Origin Location')
                                            ->badge()
                                            ->icon('heroicon-o-map-pin'),
                                    ]),
                                ]),

                            // SECTION 4: Hardware & Browser Fingerprint
                            Section::make('Hardware & Client Environment')
                                ->icon('heroicon-o-computer-desktop')
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('device')
                                            ->label('Device Type')
                                            ->badge()
                                            ->icon('heroicon-o-device-phone-mobile'),
                                        TextEntry::make('platform')
                                            ->label('Operating System')
                                            ->badge()
                                            ->color('gray'),
                                        TextEntry::make('browser')
                                            ->label('Browser')
                                            ->badge()
                                            ->color('info'),
                                    ]),
                                    TextEntry::make('user_agent')
                                        ->label('Raw User-Agent Header')
                                        ->columnSpanFull()
                                        ->fontFamily(\Filament\Support\Enums\FontFamily::Mono)
                                        ->placeholder('-')
                                        ->copyable(),
                                ]),
                        ]),

                        // SECTION 5: Request Telemetry
                        Section::make('Request Endpoint & Payload Metadata')
                            ->icon('heroicon-o-code-bracket')
                            ->collapsible()
                            ->schema([
                                Grid::make(3)->schema([
                                    TextEntry::make('request_method')
                                        ->label('HTTP Method')
                                        ->badge()
                                        ->placeholder('SYSTEM'),
                                    TextEntry::make('request_url')
                                        ->label('Request URI / Endpoint')
                                        ->columnSpan(2)
                                        ->placeholder('-')
                                        ->copyable(),
                                ]),
                            ]),
                    ]),
            ])
            ->toolbarActions([
                Action::make('export')
                    ->label('Export Audit CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function ($livewire): StreamedResponse {
                        $records = $livewire->getFilteredTableQuery()->with(['user', 'admin'])->get();
                        $filename = 'system-activity-audit-' . now()->format('Y-m-d_His') . '.csv';

                        return response()->streamDownload(function () use ($records) {
                            $file = fopen('php://output', 'w');
                            fputs($file, "\xEF\xBB\xBF");
                            fputcsv($file, [
                                'ID',
                                'Timestamp',
                                'Actor Role',
                                'Actor Name',
                                'Actor Email',
                                'Action / Event',
                                'Category',
                                'Description',
                                'IP Address',
                                'Location',
                                'Device',
                                'Operating System',
                                'Browser',
                                'HTTP Method',
                                'Request URL',
                            ]);

                            foreach ($records as $record) {
                                fputcsv($file, [
                                    $record->id,
                                    $record->created_at?->format('Y-m-d H:i:s'),
                                    $record->actor_role,
                                    $record->actor_name,
                                    $record->actor_email,
                                    $record->action,
                                    $record->event_category,
                                    $record->description,
                                    $record->ip_address ?: 'N/A',
                                    $record->location,
                                    $record->device,
                                    $record->platform,
                                    $record->browser,
                                    $record->request_method ?: 'N/A',
                                    $record->request_url ?: 'N/A',
                                ]);
                            }
                            fclose($file);
                        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivityLogs::route('/'),
        ];
    }
}
