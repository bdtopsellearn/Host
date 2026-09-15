<?php

namespace App\Filament\Resources\PackageAddons\Pages;

use App\Filament\Resources\PackageAddons\PackageAddonResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPackageAddon extends EditRecord
{
    protected static string $resource = PackageAddonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
