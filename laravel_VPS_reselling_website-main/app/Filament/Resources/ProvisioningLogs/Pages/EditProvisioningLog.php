<?php

namespace App\Filament\Resources\ProvisioningLogs\Pages;

use App\Filament\Resources\ProvisioningLogs\ProvisioningLogResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProvisioningLog extends EditRecord
{
    protected static string $resource = ProvisioningLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
