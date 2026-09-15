<?php

namespace App\Filament\Customer\Resources\SupportTickets\Pages;

use App\Filament\Customer\Resources\SupportTickets\SupportTicketResource;
use App\Models\TicketReply;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewSupportTicket extends ViewRecord
{
    protected static string $resource = SupportTicketResource::class;

    protected string $view = 'filament.pages.view-support-ticket';

    public string $quickReplyMessage = '';

    public function sendQuickReply(): void
    {
        $message = trim($this->quickReplyMessage);
        if (empty($message)) {
            Notification::make()->title('Please enter a message before sending')->warning()->send();
            return;
        }

        TicketReply::create([
            'support_ticket_id' => $this->record->id,
            'user_id' => auth()->id(),
            'message' => $message,
        ]);

        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'TICKET_REPLIED',
            'description' => "Posted reply on support ticket [{$this->record->formatted_id}] '{$this->record->subject}'",
            'ip_address' => request()->ip(),
        ]);

        $this->record->update([
            'status' => 'in_progress',
        ]);

        $this->record->refresh();

        $this->quickReplyMessage = '';

        Notification::make()
            ->title('Reply Sent')
            ->body('Your message has been added to the ticket thread.')
            ->success()
            ->send();
    }

    public function getTitle(): string
    {
        return "[{$this->record->formatted_id}] " . $this->record->subject;
    }

    public function getHeading(): string
    {
        return $this->record->subject;
    }

    public function getSubheading(): ?string
    {
        return "Ticket {$this->record->formatted_id} · Created " . $this->record->created_at->format('M d, Y · H:i T');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reply')
                ->label('Reply to Ticket')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('primary')
                ->modalHeading('Submit Reply to Support')
                ->modalDescription('Post a reply to this support ticket. Our cloud engineering team will be notified.')
                ->modalSubmitActionLabel('Send Reply')
                ->form([
                    Textarea::make('message')
                        ->label('Your Message')
                        ->placeholder('Type your reply or additional information here...')
                        ->required()
                        ->rows(5),
                ])
                ->visible(fn () => $this->record->status !== 'closed')
                ->action(function (array $data) {
                    TicketReply::create([
                        'support_ticket_id' => $this->record->id,
                        'user_id' => auth()->id(),
                        'message' => $data['message'],
                    ]);

                    \App\Models\UserActivityLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'TICKET_REPLIED',
                        'description' => "Posted reply on support ticket [{$this->record->formatted_id}] '{$this->record->subject}'",
                        'ip_address' => request()->ip(),
                    ]);

                    $this->record->update([
                        'status' => 'in_progress',
                    ]);

                    Notification::make()
                        ->title('Reply Sent')
                        ->body('Your reply has been added to the ticket. Our team will respond shortly.')
                        ->success()
                        ->send();
                }),

            Action::make('close_ticket')
                ->label('Close Ticket')
                ->icon('heroicon-o-check')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Close Support Ticket')
                ->modalDescription('Are you sure your issue is resolved and you want to close this ticket?')
                ->modalSubmitActionLabel('Yes, Close Ticket')
                ->visible(fn () => $this->record->status !== 'closed')
                ->action(function () {
                    $this->record->update(['status' => 'closed']);

                    \App\Models\UserActivityLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'TICKET_CLOSED',
                        'description' => "Customer closed support ticket [{$this->record->formatted_id}] '{$this->record->subject}'",
                        'ip_address' => request()->ip(),
                    ]);

                    Notification::make()
                        ->title('Ticket Closed')
                        ->body('This ticket has been marked as closed.')
                        ->success()
                        ->send();
                }),

            Action::make('reopen_ticket')
                ->label('Reopen Ticket')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Reopen Support Ticket')
                ->modalDescription('Would you like to reopen this ticket for further assistance?')
                ->modalSubmitActionLabel('Reopen Ticket')
                ->visible(fn () => $this->record->status === 'closed')
                ->action(function () {
                    $this->record->update(['status' => 'open']);

                    \App\Models\UserActivityLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'TICKET_REOPENED',
                        'description' => "Customer reopened support ticket [{$this->record->formatted_id}] '{$this->record->subject}'",
                        'ip_address' => request()->ip(),
                    ]);

                    Notification::make()
                        ->title('Ticket Reopened')
                        ->body('This ticket is now active again.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
