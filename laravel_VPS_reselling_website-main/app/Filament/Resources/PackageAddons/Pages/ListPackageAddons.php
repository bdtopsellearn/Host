<?php

namespace App\Filament\Resources\PackageAddons\Pages;

use App\Filament\Resources\PackageAddons\PackageAddonResource;
use App\Models\PackageAddon;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPackageAddons extends ListRecords
{
    protected static string $resource = PackageAddonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Addon / Override'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Categories')
                ->badge(PackageAddon::count()),
            'os' => Tab::make('Operating Systems')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'os'))
                ->badge(PackageAddon::where('type', 'os')->count()),
            'region' => Tab::make('Datacenter Regions')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'region'))
                ->badge(PackageAddon::where('type', 'region')->count()),
            'storage' => Tab::make('NVMe Storage')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'storage'))
                ->badge(PackageAddon::where('type', 'storage')->count()),
            'features' => Tab::make('Backups & VPC')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('type', ['backup', 'network', 'feature']))
                ->badge(PackageAddon::whereIn('type', ['backup', 'network', 'feature'])->count()),
        ];
    }
}
