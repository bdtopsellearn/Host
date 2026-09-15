<?php

namespace App\Filament\Resources\Invoices;

use App\Filament\Resources\Invoices\Pages\CreateInvoice;
use App\Filament\Resources\Invoices\Pages\EditInvoice;
use App\Filament\Resources\Invoices\Pages\ListInvoices;
use App\Filament\Resources\Invoices\Schemas\InvoiceForm;
use App\Filament\Resources\Invoices\Tables\InvoicesTable;
use App\Models\Invoice;
use BackedEnum;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|BackedEnum|null $navigationIcon = null;

    protected static string|\UnitEnum|null $navigationGroup = 'Invoices';

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make('All Invoices')
                ->group('Invoices')
                ->sort(1)
                ->url(static::getUrl('index'))
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.invoices.index') && !request()->has('tab')),

            NavigationItem::make('Pending Approval')
                ->group('Invoices')
                ->sort(2)
                ->badge(fn () => Invoice::whereIn('status', ['pending', 'unpaid'])->whereNotNull('crypto_txid')->where('crypto_txid', '!=', '')->count() ?: null, color: 'warning')
                ->url(static::getUrl('index', ['tab' => 'pending_approval']))
                ->isActiveWhen(fn (): bool => request()->get('tab') === 'pending_approval'),

            NavigationItem::make('Unpaid Invoices')
                ->group('Invoices')
                ->sort(3)
                ->badge(fn () => Invoice::whereIn('status', ['pending', 'unpaid'])->where(fn ($q) => $q->whereNull('crypto_txid')->orWhere('crypto_txid', ''))->count() ?: null, color: 'danger')
                ->url(static::getUrl('index', ['tab' => 'unpaid']))
                ->isActiveWhen(fn (): bool => request()->get('tab') === 'unpaid'),

            NavigationItem::make('Paid Invoices')
                ->group('Invoices')
                ->sort(4)
                ->badge(fn () => Invoice::where('status', 'paid')->count() ?: null, color: 'success')
                ->url(static::getUrl('index', ['tab' => 'paid']))
                ->isActiveWhen(fn (): bool => request()->get('tab') === 'paid'),

            NavigationItem::make('Cancelled Invoices')
                ->group('Invoices')
                ->sort(5)
                ->url(static::getUrl('index', ['tab' => 'cancelled']))
                ->isActiveWhen(fn (): bool => request()->get('tab') === 'cancelled'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return InvoiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvoicesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return \App\Filament\Resources\Invoices\Infolists\InvoiceInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvoices::route('/'),
            'create' => CreateInvoice::route('/create'),
            'view' => \App\Filament\Resources\Invoices\Pages\ViewInvoice::route('/{record}'),
            'edit' => EditInvoice::route('/{record}/edit'),
        ];
    }
}
