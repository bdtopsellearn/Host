<?php

namespace App\Console\Commands;

use App\Mail\ServerTerminatedMail;
use App\Models\Invoice;
use App\Models\ProvisioningLog;
use App\Models\Service;
use App\Services\Provisioning\ProvisioningServiceInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnforceExpirationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:enforce-expirations {--dry-run : Simulate execution without cancelling instances or updating database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enforce strict reseller zero-grace-period termination on expired services past their renewal due date';

    /**
     * Execute the console command.
     */
    public function handle(ProvisioningServiceInterface $provisioning): int
    {
        $isDryRun = (bool) $this->option('dry-run');
        $this->info("Scanning services for overdue expiration enforcement (Dry-Run: " . ($isDryRun ? 'YES' : 'NO') . ")...");

        $services = Service::with(['user', 'package'])
            ->whereIn('status', ['active', 'suspended'])
            ->whereNotNull('next_due_date')
            ->get();

        $terminatedCount = 0;
        $today = now()->startOfDay();

        foreach ($services as $service) {
            $dueDate = Carbon::parse($service->next_due_date)->startOfDay();

            // Only process services whose due date has completely elapsed (past due date)
            if ($dueDate->gte($today)) {
                continue;
            }

            // Verify if there is a paid invoice covering or extending beyond this due date
            $hasPaidRenewal = Invoice::where('service_id', $service->id)
                ->where('status', 'paid')
                ->whereDate('due_date', '>=', $dueDate)
                ->exists();

            if ($hasPaidRenewal) {
                // If paid invoice exists, ensure next_due_date is synchronized and skip termination
                continue;
            }

            $userEmail = $service->user?->email ?? 'N/A';
            $instanceId = $service->contabo_instance_id ?? 'No Instance ID';

            if ($isDryRun) {
                $this->warn("[DRY-RUN] Would terminate Service #{$service->id} ({$service->server_name}, IP: {$service->ip_address}, Due: {$dueDate->toDateString()}) for {$userEmail}");
                $terminatedCount++;
                continue;
            }

            $this->error("Terminating expired Service #{$service->id} (Due: {$dueDate->toDateString()}) - Reseller Zero Grace Period Policy");

            // 1. Cancel upstream instance with provider (Contabo / Mock)
            if (!empty($service->contabo_instance_id)) {
                try {
                    $result = $provisioning->cancelInstance((string) $service->contabo_instance_id);

                    ProvisioningLog::create([
                        'service_id' => $service->id,
                        'action' => 'terminate_due_to_non_renewal',
                        'request_payload' => [
                            'instance_id' => $service->contabo_instance_id,
                            'reason' => 'zero_grace_period_expired',
                            'due_date' => $service->next_due_date,
                        ],
                        'response_payload' => $result->rawResponse ?? ['message' => $result->message],
                        'is_success' => $result->success,
                    ]);
                } catch (\Throwable $e) {
                    Log::error("Failed to cancel upstream instance {$service->contabo_instance_id} for Service #{$service->id}: " . $e->getMessage());
                }
            }

            // 2. Update service status to terminated
            $service->update(['status' => 'terminated']);

            // 3. Mark any outstanding unpaid invoices for this service as cancelled
            Invoice::where('service_id', $service->id)
                ->where('status', 'unpaid')
                ->update(['status' => 'cancelled']);

            // 4. Send termination notification email to customer
            if ($service->user) {
                try {
                    Mail::to($service->user->email)->send(new ServerTerminatedMail($service, $service->user));
                    $this->line("Sent ServerTerminatedMail to {$service->user->email}");
                } catch (\Throwable $e) {
                    Log::error("Failed to send ServerTerminatedMail for Service #{$service->id}: " . $e->getMessage());
                }
            }

            $terminatedCount++;
        }

        $this->info("Completed expiration enforcement: {$terminatedCount} overdue services terminated.");
        return Command::SUCCESS;
    }
}
