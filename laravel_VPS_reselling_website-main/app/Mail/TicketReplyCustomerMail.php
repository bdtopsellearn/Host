<?php

namespace App\Mail;

use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketReplyCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public SupportTicket $ticket;
    public TicketReply $reply;
    public User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(SupportTicket $ticket, TicketReply $reply, User $user)
    {
        $this->ticket = $ticket;
        $this->reply = $reply;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Staff Reply: [{$this->ticket->formatted_id}] {$this->ticket->subject}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-reply-customer',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
