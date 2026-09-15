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

class RenewalReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Service $service;
    public Invoice $invoice;
    public User $user;
    public int $daysRemaining;

    /**
     * Create a new message instance.
     */
    public function __construct(Service $service, Invoice $invoice, User $user, int $daysRemaining)
    {
        $this->service = $service;
        $this->invoice = $invoice;
        $this->user = $user;
        $this->daysRemaining = $daysRemaining;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $packageName = $this->service->package->name ?? 'VPS Service';
        $invoiceNum = $this->invoice->invoice_number;

        $subject = match ($this->daysRemaining) {
            0 => "🚨 FINAL NOTICE: Your {$packageName} Renews TODAY (Invoice {$invoiceNum})",
            3 => "⚠️ Urgent Reminder: Your {$packageName} Renews in 3 Days (Invoice {$invoiceNum})",
            default => "Reminder: Your {$packageName} Renews in {$this->daysRemaining} Days (Invoice {$invoiceNum})",
        };

        return new Envelope(subject: $subject);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.renewal-reminder',
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
