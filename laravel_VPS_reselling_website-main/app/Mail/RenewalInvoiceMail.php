<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Service;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RenewalInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public Service $service;
    public Invoice $invoice;
    public User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(Service $service, Invoice $invoice, User $user)
    {
        $this->service = $service;
        $this->invoice = $invoice;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $packageName = $this->service->package->name ?? 'VPS Service';
        return new Envelope(
            subject: "Renewal Invoice {$this->invoice->invoice_number} Generated for {$packageName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.renewal-invoice',
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
