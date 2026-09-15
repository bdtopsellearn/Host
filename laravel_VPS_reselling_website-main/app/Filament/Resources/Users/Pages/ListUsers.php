<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

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
            'all' => Tab::make('All Users')
                ->badge(User::count()),
            'active' => Tab::make('Active Users')
                ->modifyQueryUsing(fn (Builder $query) => $query->activeCustomer())
                ->badge(User::activeCustomer()->count())
                ->badgeColor('success'),
            'suspended' => Tab::make('Suspended Users')
                ->modifyQueryUsing(fn (Builder $query) => $query->suspendedCustomer())
                ->badge(User::suspendedCustomer()->count())
                ->badgeColor('warning'),
            'inactive' => Tab::make('Inactive Users')
                ->modifyQueryUsing(fn (Builder $query) => $query->inactiveCustomer())
                ->badge(User::inactiveCustomer()->count())
                ->badgeColor('gray'),
        ];
    }
}
