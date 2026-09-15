<?php

namespace App\Filament\Customer\Resources\SupportTickets\Pages;

use App\Filament\Customer\Resources\SupportTickets\SupportTicketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSupportTickets extends ListRecords
{
    protected static string $resource = SupportTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Open New Ticket')
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
