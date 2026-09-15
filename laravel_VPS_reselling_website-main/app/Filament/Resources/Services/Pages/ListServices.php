<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use App\Models\Service;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    public function getTitle(): string
    {
        return 'VPS Services';
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return request()->query('tab', 'all');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All VPS Services')
                ->badge(fn () => Service::count()),
            'active' => Tab::make('Active VPS')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active'))
                ->badge(Service::where('status', 'active')->count())
                ->badgeColor('success'),
            'awaiting_provisioning' => Tab::make('Awaiting Provisioning')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['awaiting_provisioning', 'provisioning']))
                ->badge(Service::whereIn('status', ['awaiting_provisioning', 'provisioning'])->count())
                ->badgeColor('warning'),
            'suspended' => Tab::make('Suspended')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'suspended'))
                ->badge(Service::where('status', 'suspended')->count())
                ->badgeColor('danger'),
            'terminated' => Tab::make('Terminated / Cancelled')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['terminated', 'cancelled']))
                ->badge(Service::whereIn('status', ['terminated', 'cancelled'])->count()),
        ];
    }
}
