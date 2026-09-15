<?php

namespace App\Mail;

use App\Models\Service;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServerTerminatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Service $service;
    public User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(Service $service, User $user)
    {
        $this->service = $service;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $packageName = $this->service->package->name ?? 'VPS Service';
        return new Envelope(
            subject: "Notice of Server Cancellation & Termination - {$packageName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.server-terminated',
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
