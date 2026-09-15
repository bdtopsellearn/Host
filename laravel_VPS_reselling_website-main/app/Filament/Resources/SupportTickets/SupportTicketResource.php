<?php

namespace App\Filament\Resources\SupportTickets;

use App\Filament\Resources\SupportTickets\Pages\CreateSupportTicket;
use App\Filament\Resources\SupportTickets\Pages\EditSupportTicket;
use App\Filament\Resources\SupportTickets\Pages\ListSupportTickets;
use App\Filament\Resources\SupportTickets\Pages\ViewSupportTicket;
use App\Filament\Resources\SupportTickets\Schemas\SupportTicketForm;
use App\Filament\Resources\SupportTickets\Tables\SupportTicketsTable;
use App\Models\SupportTicket;
use BackedEnum;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Support Tickets';

    protected static string|BackedEnum|null $navigationIcon = null;

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make('All Tickets')
                ->group('Support Tickets')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.support-tickets.*') && (!request()->filled('tab') || request()->query('tab') === 'all'))
                ->badge(fn (): ?int => SupportTicket::count() ?: null)
                ->sort(1)
                ->url(static::getUrl('index')),

            NavigationItem::make('Active Tickets')
                ->group('Support Tickets')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.support-tickets.*') && request()->query('tab') === 'active')
                ->badge(fn (): ?int => SupportTicket::whereIn('status', ['open', 'in_progress'])->count() ?: null, color: 'warning')
                ->sort(2)
                ->url(static::getUrl('index', ['tab' => 'active'])),

            NavigationItem::make('Answered')
                ->group('Support Tickets')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.support-tickets.*') && request()->query('tab') === 'answered')
                ->badge(fn (): ?int => SupportTicket::where('status', 'answered')->count() ?: null, color: 'info')
                ->sort(3)
                ->url(static::getUrl('index', ['tab' => 'answered'])),

            NavigationItem::make('Closed / Inactive')
                ->group('Support Tickets')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.support-tickets.*') && request()->query('tab') === 'closed')
                ->badge(fn (): ?int => SupportTicket::where('status', 'closed')->count() ?: null, color: 'gray')
                ->sort(4)
                ->url(static::getUrl('index', ['tab' => 'closed'])),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return SupportTicketForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SupportTicketsTable::configure($table);
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
            'index' => ListSupportTickets::route('/'),
            'create' => CreateSupportTicket::route('/create'),
            'view' => ViewSupportTicket::route('/{record}'),
            'edit' => EditSupportTicket::route('/{record}/edit'),
        ];
    }
}
