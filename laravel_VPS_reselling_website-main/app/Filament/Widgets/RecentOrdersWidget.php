<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Cloud VPS Orders';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::with(['user', 'items.package'])->latest()->limit(6))
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #')
                    ->badge()
                    ->color('primary')
                    ->weight(FontWeight::Bold),

                TextColumn::make('user.name')
                    ->label('Client')
                    ->description(fn (Order $record): ?string => $record->user?->email)
                    ->weight(FontWeight::Medium),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('USD')
                    ->weight(FontWeight::Bold),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => fn ($state): bool => in_array($state, ['pending', 'pending_approval', 'payment_confirmed']),
                        'success' => fn ($state): bool => in_array($state, ['active', 'completed', 'provision', 'contabo_ok']),
                        'danger' => fn ($state): bool => in_array($state, ['cancelled', 'failed']),
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending Payment',
                        'pending_approval' => 'Pending Review',
                        'payment_confirmed' => 'Ready to Deploy',
                        'active', 'completed', 'contabo_ok', 'provision' => 'Active / Deployed',
                        'cancelled' => 'Cancelled',
                        'failed' => 'Failed',
                        default => ucfirst($state),
                    }),

                TextColumn::make('created_at')
                    ->label('Placed')
                    ->since()
                    ->color('gray'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
