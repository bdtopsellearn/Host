<?php

namespace App\Filament\Customer\Resources\Services\Tables;

use App\Filament\Customer\Resources\Services\ServiceResource;
use App\Models\Service;
use Filament\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.order_number')
                    ->label('Order #')
                    ->badge()
                    ->color('gray')
                    ->placeholder('N/A')
                    ->weight(FontWeight::Bold)
                    ->copyable()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('formatted_hostname')
                    ->label('Product / Service')
                    ->weight(FontWeight::Bold)
                    ->formatStateUsing(function ($state, Service $record) {
                        $plan = $record->package?->name ?? 'Cloud VPS';
                        return "{$state} ({$plan})";
                    })
                    ->description(fn (Service $record) => $record->specs_summary ? "{$record->specs_summary} • {$record->formatted_region}" : $record->formatted_region)
                    ->copyable()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('formatted_pricing')
                    ->label('Pricing')
                    ->weight(FontWeight::Bold)
                    ->description(fn (Service $record) => $record->formatted_billing_cycle)
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('recurring_amount', $direction)),

                TextColumn::make('next_due_date')
                    ->label('Next Due Date')
                    ->date('M j, Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'contabo_ok' => 'info',
                        'provisioning', 'awaiting_provisioning', 'pending' => 'warning',
                        'suspended' => 'danger',
                        'terminated', 'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending (Unpaid)',
                        'awaiting_provisioning' => 'Awaiting Provisioning',
                        'provisioning', 'contabo_ok' => 'Provisioning',
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'terminated' => 'Terminated',
                        'cancelled' => 'Cancelled',
                        default => ucwords(str_replace('_', ' ', $state)),
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (Service $record) => ServiceResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                Action::make('manage')
                    ->label('Manage')
                    ->icon('heroicon-m-cog-6-tooth')
                    ->button()
                    ->color('primary')
                    ->url(fn (Service $record) => ServiceResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
