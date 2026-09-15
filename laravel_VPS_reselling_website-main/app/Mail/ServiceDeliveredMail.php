<?php

namespace App\Mail;

use App\Models\Service;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceDeliveredMail extends Mailable
{
    use Queueable, SerializesModels;

    public Service $service;
    public User $user;
    public string $password;
    public string $username;

    /**
     * Create a new message instance.
     */
    public function __construct(Service $service, User $user, string $password, string $username = 'root')
    {
        $this->service = $service;
        $this->user = $user;
        $this->password = $password;
        $this->username = $username;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $packageName = $this->service->package->name ?? 'VPS Service';
        return new Envelope(
            subject: "Your {$packageName} is Ready! Server Login & Connection Details",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.service_delivered',
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
