<?php

namespace App\Console\Commands;

use App\Mail\RenewalInvoiceMail;
use App\Mail\RenewalReminderMail;
use App\Models\Invoice;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessRenewalsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:process-renewals {--dry-run : Simulate execution without writing to database or sending emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process upcoming service renewals, auto-generate invoices at T-14 days, and dispatch dunning email reminders at T-7, T-3, and Due Date';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');
        $this->info("Scanning services for renewal processing (Dry-Run: " . ($isDryRun ? 'YES' : 'NO') . ")...");

        $services = Service::with(['user', 'package'])
            ->whereIn('status', ['active', 'suspended'])
            ->whereNotNull('next_due_date')
            ->get();

        $invoicesGenerated = 0;
        $remindersSent = 0;
        $today = now()->startOfDay();

        foreach ($services as $service) {
            if (!$service->user) {
                continue;
            }

            $dueDate = Carbon::parse($service->next_due_date)->startOfDay();
            $daysRemaining = (int) $today->diffInDays($dueDate, false);

            // Outside the 14-day renewal window or already past due
            if ($daysRemaining > 14 || $daysRemaining < 0) {
                continue;
            }

            // Find existing invoice for this service covering this upcoming due date
            $existingInvoice = Invoice::where('service_id', $service->id)
                ->whereDate('due_date', $dueDate)
                ->first();

            // 1. Stage T-14: Generate Renewal Invoice if none exists
            if (!$existingInvoice) {
                $amount = (float) ($service->recurring_amount ?? $service->package?->price ?? 0);

                if ($isDryRun) {
                    $this->line("[DRY-RUN] Would generate renewal invoice for Service #{$service->id} ({$service->server_name}) - \${$amount} due on {$dueDate->toDateString()}");
                    $invoicesGenerated++;
                } else {
                    $invoice = Invoice::create([
                        'user_id' => $service->user_id,
                        'order_id' => $service->order_id,
                        'service_id' => $service->id,
                        'amount' => $amount,
                        'tax' => 0.00,
                        'total' => $amount,
                        'status' => 'unpaid',
                        'due_date' => $service->next_due_date,
                        'dunning_reminders' => ['14' => now()->toIso8601String()],
                    ]);

                    try {
                        Mail::to($service->user->email)->send(new RenewalInvoiceMail($service, $invoice, $service->user));
                        $this->info("Generated renewal invoice {$invoice->invoice_number} and emailed to {$service->user->email}");
                    } catch (\Throwable $e) {
                        Log::error("Failed to send RenewalInvoiceMail for invoice {$invoice->invoice_number}: " . $e->getMessage());
                        $this->error("Failed to send email to {$service->user->email}: " . $e->getMessage());
                    }

                    $invoicesGenerated++;
                }
                continue;
            }

            // 2. Stages T-7, T-3, Due Date: Send Dunning Reminders if invoice is still unpaid
            if ($existingInvoice->status === 'unpaid') {
                $reminders = $existingInvoice->dunning_reminders ?? [];
                $stageToSend = null;

                if ($daysRemaining <= 7 && $daysRemaining > 3 && !isset($reminders['7'])) {
                    $stageToSend = 7;
                } elseif ($daysRemaining <= 3 && $daysRemaining > 0 && !isset($reminders['3'])) {
                    $stageToSend = 3;
                } elseif ($daysRemaining === 0 && !isset($reminders['0'])) {
                    $stageToSend = 0;
                }

                if ($stageToSend !== null) {
                    if ($isDryRun) {
                        $this->line("[DRY-RUN] Would send Stage-{$stageToSend} reminder for Invoice {$existingInvoice->invoice_number} to {$service->user->email}");
                        $remindersSent++;
                    } else {
                        try {
                            Mail::to($service->user->email)->send(new RenewalReminderMail($service, $existingInvoice, $service->user, $stageToSend));
                            $reminders[(string) $stageToSend] = now()->toIso8601String();
                            $existingInvoice->update(['dunning_reminders' => $reminders]);
                            $this->info("Dispatched {$stageToSend}-day renewal reminder for Invoice {$existingInvoice->invoice_number} to {$service->user->email}");
                        } catch (\Throwable $e) {
                            Log::error("Failed to send RenewalReminderMail for invoice {$existingInvoice->invoice_number}: " . $e->getMessage());
                            $this->error("Failed to send reminder email to {$service->user->email}: " . $e->getMessage());
                        }

                        $remindersSent++;
                    }
                }
            }
        }

        $this->info("Completed renewal processing: {$invoicesGenerated} invoices generated, {$remindersSent} reminders dispatched.");
        return Command::SUCCESS;
    }
}
