<?php

namespace App\Filament\Resources\SupportTickets\Pages;

use App\Filament\Resources\SupportTickets\SupportTicketResource;
use App\Models\SupportTicket;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSupportTickets extends ListRecords
{
    protected static string $resource = SupportTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Tickets')
                ->badge(fn () => SupportTicket::count() ?: null),
            'active' => Tab::make('Active / Open')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['open', 'in_progress']))
                ->badge(fn () => SupportTicket::whereIn('status', ['open', 'in_progress'])->count() ?: null)
                ->badgeColor('warning'),
            'answered' => Tab::make('Answered')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'answered'))
                ->badge(fn () => SupportTicket::where('status', 'answered')->count() ?: null)
                ->badgeColor('info'),
            'closed' => Tab::make('Closed / Inactive')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'closed'))
                ->badge(fn () => SupportTicket::where('status', 'closed')->count() ?: null)
                ->badgeColor('gray'),
        ];
    }
}
