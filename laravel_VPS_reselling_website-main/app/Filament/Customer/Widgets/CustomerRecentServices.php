<?php

namespace App\Filament\Customer\Widgets;

use App\Filament\Customer\Resources\Services\ServiceResource;
use App\Models\Service;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CustomerRecentServices extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('My Cloud Servers')
            ->description('Active virtual machines and network configurations.')
            ->query(
                Service::query()->where('user_id', auth()->id())->latest()
            )
            ->columns([
                TextColumn::make('package.name')
                    ->label('Cloud Plan')
                    ->badge()
                    ->color('primary')
                    ->weight('bold'),

                TextColumn::make('ip_address')
                    ->label('Dedicated IPv4')
                    ->copyable()
                    ->copyMessage('IP copied to clipboard')
                    ->placeholder('Allocating IP...')
                    ->weight('bold'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active', 'contabo_ok', 'provisioned' => 'success',
                        'awaiting_provisioning', 'provisioning' => 'warning',
                        'suspended' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active', 'contabo_ok', 'provisioned' => 'Active',
                        'awaiting_provisioning', 'provisioning' => 'Provisioning',
                        'suspended' => 'Suspended',
                        default => ucfirst($state),
                    }),

                TextColumn::make('billing_cycle')
                    ->label('Billing Cycle')
                    ->formatStateUsing(fn (?string $state): string => $state ? ucfirst($state) : 'Monthly'),

                TextColumn::make('recurring_amount')
                    ->label('Renewal Rate')
                    ->money('USD'),

                TextColumn::make('next_due_date')
                    ->label('Next Renewal Date')
                    ->date()
                    ->placeholder('N/A'),
            ])
            ->recordActions([
                Action::make('manage')
                    ->label('Manage Server')
                    ->button()
                    ->size('sm')
                    ->color('primary')
                    ->url(fn (Service $record): string => ServiceResource::getUrl('view', ['record' => $record])),
            ])
            ->emptyStateHeading('No Cloud Servers Yet')
            ->emptyStateDescription('Deploy your high-performance NVMe cloud VPS in under 60 seconds.')
            ->emptyStateActions([
                Action::make('deploy')
                    ->label('+ Deploy Server')
                    ->button()
                    ->color('primary')
                    ->url(url('/plans'))
                    ->openUrlInNewTab(),
            ])
            ->paginated([5]);
    }
}
