<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'service_id',
        'invoice_number',
        'amount',
        'tax',
        'total',
        'status',
        'payment_method',
        'crypto_network',
        'crypto_wallet_address',
        'crypto_txid',
        'sender_number',
        'transaction_id',
        'payment_notes',
        'stripe_payment_intent_id',
        'due_date',
        'dunning_reminders',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'due_date' => 'date',
            'dunning_reminders' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateNextNumber();
            }
        });

        static::updated(function (Invoice $invoice) {
            if ($invoice->wasChanged('status') && $invoice->status === 'paid') {
                if ($invoice->service_id) {
                    $invoice->service?->extendBillingCycle();
                }

                // Log customer activity footprint for invoice settlement / credit added
                if ($invoice->user_id) {
                    if (empty($invoice->order_id) && empty($invoice->service_id)) {
                        \App\Models\UserActivityLog::create([
                            'user_id' => $invoice->user_id,
                            'action' => 'CREDIT_ADDED',
                            'description' => "Account credit balance credited with \${$invoice->total} USD (Deposit Invoice #{$invoice->invoice_number})",
                            'ip_address' => request()->ip(),
                        ]);
                    } elseif ($invoice->service_id) {
                        \App\Models\UserActivityLog::create([
                            'user_id' => $invoice->user_id,
                            'action' => 'SERVICE_RENEWED',
                            'description' => "Service renewed: Invoice #{$invoice->invoice_number} paid (\${$invoice->total} USD)",
                            'ip_address' => request()->ip(),
                        ]);
                    } else {
                        \App\Models\UserActivityLog::create([
                            'user_id' => $invoice->user_id,
                            'action' => 'INVOICE_PAID',
                            'description' => "Invoice #{$invoice->invoice_number} paid in full (\${$invoice->total} USD)",
                            'ip_address' => request()->ip(),
                        ]);
                    }
                }

                if ($invoice->user) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($invoice->user->email)->send(
                            new \App\Mail\InvoiceReceiptMail($invoice, $invoice->user)
                        );
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::error("Failed to send InvoiceReceiptMail for invoice #{$invoice->invoice_number}: " . $e->getMessage());
                    }
                }
            }
        });
    }

    /**
     * Generate a clean, sequential, and easily understandable invoice number (e.g., INV-10001, INV-10002).
     */
    public static function generateNextNumber(): string
    {
        $invoices = static::query()
            ->where('invoice_number', 'LIKE', 'INV-%')
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        $maxNumber = 10000;
        foreach ($invoices as $inv) {
            if (preg_match('/^INV-(\d+)$/', $inv->invoice_number, $matches)) {
                $num = intval($matches[1]);
                if ($num > $maxNumber) {
                    $maxNumber = $num;
                }
            }
        }

        if ($maxNumber === 10000) {
            $maxId = static::max('id') ?? 0;
            $nextSeq = 10000 + $maxId + 1;
        } else {
            $nextSeq = $maxNumber + 1;
        }

        while (static::where('invoice_number', 'INV-' . $nextSeq)->exists()) {
            $nextSeq++;
        }

        return 'INV-' . $nextSeq;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
