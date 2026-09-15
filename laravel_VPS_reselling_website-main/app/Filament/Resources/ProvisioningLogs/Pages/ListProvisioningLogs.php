<?php

namespace App\Filament\Resources\ProvisioningLogs\Pages;

use App\Filament\Resources\ProvisioningLogs\ProvisioningLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProvisioningLogs extends ListRecords
{
    protected static string $resource = ProvisioningLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
