<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ActivityLogs\ActivityLogResource;
use App\Models\UserActivityLog;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LiveActivityForensicsWidget extends BaseWidget
{
    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Live Telemetry & Security Forensics';

    public function table(Table $table): Table
    {
        return $table
            ->query(UserActivityLog::latest()->limit(6))
            ->columns([
                TextColumn::make('action')
                    ->label('Event')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        str_contains($state, 'ADMIN') => 'primary',
                        str_contains($state, 'LOGIN') => 'success',
                        str_contains($state, 'FAIL') || str_contains($state, 'ERROR') => 'danger',
                        str_contains($state, 'ORDER') || str_contains($state, 'DEPOSIT') => 'warning',
                        default => 'info',
                    })
                    ->weight(FontWeight::Bold),

                TextColumn::make('actor_type')
                    ->label('Actor')
                    ->badge()
                    ->colors([
                        'primary' => 'admin',
                        'gray' => 'user',
                        'warning' => 'system',
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state ?? 'Client')),

                TextColumn::make('description')
                    ->label('Narrative')
                    ->limit(35)
                    ->tooltip(fn (UserActivityLog $record): string => (string) $record->description)
                    ->description(fn (UserActivityLog $record): ?string => $record->ip_address),

                TextColumn::make('created_at')
                    ->label('Time')
                    ->since()
                    ->color('gray'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (UserActivityLog $record): string => ActivityLogResource::getUrl('index')),
            ])
            ->paginated(false);
    }
}
