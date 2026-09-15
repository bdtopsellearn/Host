<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return request()->query('tab', 'all');
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Orders')
                ->badge(fn () => Order::count()),
            'pending' => Tab::make('Pending Payment')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(fn () => Order::where('status', 'pending')->count() ?: null)
                ->badgeColor('gray'),
            'ready_to_deploy' => Tab::make('Ready to Deploy')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'payment_confirmed'))
                ->badge(fn () => Order::where('status', 'payment_confirmed')->count() ?: null)
                ->badgeColor('warning'),
            'active' => Tab::make('Active / Provisioned')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['active', 'completed', 'contabo_ok', 'provision']))
                ->badge(fn () => Order::whereIn('status', ['active', 'completed', 'contabo_ok', 'provision'])->count() ?: null)
                ->badgeColor('success'),
            'cancelled' => Tab::make('Cancelled / Failed')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['cancelled', 'failed']))
                ->badge(fn () => Order::whereIn('status', ['cancelled', 'failed'])->count() ?: null)
                ->badgeColor('danger'),
        ];
    }
}
