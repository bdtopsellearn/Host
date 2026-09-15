<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

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
            'all' => Tab::make('All Invoices')
                ->badge(Invoice::count()),
            'pending_approval' => Tab::make('Pending Approval')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['pending', 'unpaid'])->whereNotNull('crypto_txid')->where('crypto_txid', '!=', ''))
                ->badge(fn () => Invoice::whereIn('status', ['pending', 'unpaid'])->whereNotNull('crypto_txid')->where('crypto_txid', '!=', '')->count() ?: null)
                ->badgeColor('warning'),
            'unpaid' => Tab::make('Unpaid Invoices')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['pending', 'unpaid'])->where(fn ($q) => $q->whereNull('crypto_txid')->orWhere('crypto_txid', '')))
                ->badge(fn () => Invoice::whereIn('status', ['pending', 'unpaid'])->where(fn ($q) => $q->whereNull('crypto_txid')->orWhere('crypto_txid', ''))->count() ?: null)
                ->badgeColor('danger'),
            'paid' => Tab::make('Paid Invoices')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'paid'))
                ->badge(fn () => Invoice::where('status', 'paid')->count() ?: null)
                ->badgeColor('success'),
            'cancelled' => Tab::make('Cancelled Invoices')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['cancelled', 'refunded']))
                ->badge(fn () => Invoice::whereIn('status', ['cancelled', 'refunded'])->count() ?: null),
        ];
    }
}
