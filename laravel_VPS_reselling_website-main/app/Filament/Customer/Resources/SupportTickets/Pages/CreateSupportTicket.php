<?php

namespace App\Filament\Customer\Resources\SupportTickets\Pages;

use App\Filament\Customer\Resources\SupportTickets\SupportTicketResource;
use App\Models\TicketReply;
use Filament\Resources\Pages\CreateRecord;

class CreateSupportTicket extends CreateRecord
{
    protected static string $resource = SupportTicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status'] = 'open';
        return $data;
    }

    protected function afterCreate(): void
    {
        $message = $this->data['initial_message'] ?? $this->record->subject;

        TicketReply::create([
            'support_ticket_id' => $this->record->id,
            'user_id' => auth()->id(),
            'message' => $message,
        ]);

        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'TICKET_OPENED',
            'description' => "Opened support ticket [{$this->record->formatted_id}] '{$this->record->subject}' (Department: " . ucfirst($this->record->department) . ', Priority: ' . ucfirst($this->record->priority) . ')',
            'ip_address' => request()->ip(),
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
