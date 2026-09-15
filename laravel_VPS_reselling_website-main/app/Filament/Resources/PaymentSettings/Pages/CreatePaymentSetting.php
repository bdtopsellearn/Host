<?php

namespace App\Filament\Resources\PaymentSettings\Pages;

use App\Filament\Resources\PaymentSettings\PaymentSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentSetting extends CreateRecord
{
    protected static string $resource = PaymentSettingResource::class;
}
