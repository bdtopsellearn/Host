<?php

namespace App\Filament\Resources\SupportTickets\Pages;

use App\Filament\Resources\SupportTickets\SupportTicketResource;
use App\Mail\TicketReplyCustomerMail;
use App\Models\Admin;
use App\Models\TicketReply;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ViewSupportTicket extends ViewRecord
{
    protected static string $resource = SupportTicketResource::class;

    protected string $view = 'filament.pages.view-support-ticket';

    public string $quickReplyMessage = '';

    public function sendQuickReply(): void
    {
        $message = trim($this->quickReplyMessage);
        if (empty($message)) {
            Notification::make()->title('Please enter a response message before sending')->warning()->send();
            return;
        }

        $adminId = auth('admin')->id() ?? Admin::first()?->id;

        $reply = TicketReply::create([
            'support_ticket_id' => $this->record->id,
            'admin_id' => $adminId,
            'message' => $message,
        ]);

        $this->record->update([
            'status' => 'answered',
        ]);

        if ($this->record->user) {
            try {
                Mail::to($this->record->user->email)->send(
                    new TicketReplyCustomerMail($this->record, $reply, $this->record->user)
                );
            } catch (\Throwable $e) {
                Log::error("Failed to dispatch TicketReplyCustomerMail: " . $e->getMessage());
            }

            $staffName = auth('admin')->user()?->name ?? 'Support Staff';
            \App\Models\UserActivityLog::create([
                'user_id' => $this->record->user_id,
                'action' => 'STAFF_TICKET_REPLY',
                'description' => "Support staff ({$staffName}) replied to ticket [{$this->record->formatted_id}] '{$this->record->subject}'",
                'ip_address' => request()->ip(),
            ]);
        }

        $this->record->refresh();

        $this->quickReplyMessage = '';

        Notification::make()
            ->title('Staff Reply Posted')
            ->body("Response posted and emailed to {$this->record->user?->email}.")
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
        $customerName = $this->record->user?->name ?? 'Customer';
        return "Ticket {$this->record->formatted_id} · Customer: {$customerName} · Created " . $this->record->created_at->format('M d, Y · H:i T');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('staff_reply')
                ->label('Post Staff Reply')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('primary')
                ->modalHeading('Send Official Response to Customer')
                ->modalDescription('This response will be added to the ticket thread and emailed directly to the customer.')
                ->modalSubmitActionLabel('Send Response & Notify')
                ->form([
                    Textarea::make('message')
                        ->label('Staff Response Message')
                        ->placeholder('Type your official response to the customer here...')
                        ->required()
                        ->rows(6),
                ])
                ->action(function (array $data) {
                    $adminId = auth('admin')->id() ?? Admin::first()?->id;

                    $reply = TicketReply::create([
                        'support_ticket_id' => $this->record->id,
                        'admin_id' => $adminId,
                        'message' => $data['message'],
                    ]);

                    $this->record->update([
                        'status' => 'answered',
                    ]);

                    if ($this->record->user) {
                        try {
                            Mail::to($this->record->user->email)->send(
                                new TicketReplyCustomerMail($this->record, $reply, $this->record->user)
                            );
                        } catch (\Throwable $e) {
                            Log::error("Failed to dispatch TicketReplyCustomerMail: " . $e->getMessage());
                        }

                        $staffName = auth('admin')->user()?->name ?? 'Support Staff';
                        \App\Models\UserActivityLog::create([
                            'user_id' => $this->record->user_id,
                            'action' => 'STAFF_TICKET_REPLY',
                            'description' => "Support staff ({$staffName}) replied to ticket [{$this->record->formatted_id}] '{$this->record->subject}'",
                            'ip_address' => request()->ip(),
                        ]);
                    }

                    Notification::make()
                        ->title('Staff Reply Posted')
                        ->body("Response posted and emailed to {$this->record->user?->email}.")
                        ->success()
                        ->send();
                }),

            Action::make('change_status')
                ->label('Update Status')
                ->icon('heroicon-o-adjustments-horizontal')
                ->color('info')
                ->modalHeading('Change Ticket Status')
                ->form([
                    Select::make('status')
                        ->label('New Status')
                        ->options([
                            'open' => '🟡 Open',
                            'in_progress' => '🔵 In Progress',
                            'answered' => '🟢 Answered',
                            'closed' => '⚪ Closed',
                        ])
                        ->default($this->record->status)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->update(['status' => $data['status']]);
                    Notification::make()->title('Status Updated')->success()->send();
                }),

            Action::make('close_ticket')
                ->label('Close Ticket')
                ->icon('heroicon-o-check-circle')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Close Support Ticket')
                ->modalDescription('Mark this ticket as closed/resolved?')
                ->modalSubmitActionLabel('Close Ticket')
                ->visible(fn () => $this->record->status !== 'closed')
                ->action(function () {
                    $this->record->update(['status' => 'closed']);
                    Notification::make()->title('Ticket Closed')->success()->send();
                }),

            Action::make('reopen_ticket')
                ->label('Reopen Ticket')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Reopen Ticket')
                ->modalDescription('Set this ticket back to Open status?')
                ->modalSubmitActionLabel('Reopen')
                ->visible(fn () => $this->record->status === 'closed')
                ->action(function () {
                    $this->record->update(['status' => 'open']);
                    Notification::make()->title('Ticket Reopened')->success()->send();
                }),

            ActionGroup::make([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->label('More Actions')
            ->icon('heroicon-m-ellipsis-vertical')
            ->color('gray'),
        ];
    }
}
