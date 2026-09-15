<?php

namespace App\Filament\Customer\Resources\Invoices\Pages;

use App\Filament\Customer\Resources\Invoices\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;
}
