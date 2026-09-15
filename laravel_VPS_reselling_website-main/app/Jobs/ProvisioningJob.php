<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Provisioning\ProvisioningServiceInterface;
use App\Models\Service;
use App\Models\Order;

class ProvisioningJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [30, 60, 120];

    protected $serviceId;
    protected $action;
    protected $payload;

    /**
     * Create a new job instance.
     */
    public function __construct(int $serviceId, string $action, array $payload = [])
    {
        $this->serviceId = $serviceId;
        $this->action = $action;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(ProvisioningServiceInterface $provisioningService): void
    {
        $service = Service::find($this->serviceId);
        
        if (!$service) {
            return;
        }

        $result = null;

        switch ($this->action) {
            case 'create':
                $this->payload['service_id'] = $service->id;
                $result = $provisioningService->createInstance($this->payload);
                if ($result->success) {
                    $service->update([
                        'contabo_instance_id' => $result->data['instanceId'] ?? null,
                        'ip_address' => $result->data['ipAddress'] ?? null,
                        'encrypted_credentials' => encrypt($result->data['initialPassword'] ?? ''),
                        'default_user' => $result->data['defaultUser'] ?? 'root',
                        'status' => 'contabo_ok',
                    ]);
                    
                    Order::where('id', $service->order_id)->update(['status' => 'contabo_ok']);
                } else {
                    $service->update(['status' => 'provisioning_failed']);
                    Order::where('id', $service->order_id)->update(['status' => 'failed']);
                }
                break;
                
            case 'start':
                $result = $provisioningService->startInstance($service->contabo_instance_id);
                break;
                
            case 'stop':
                $result = $provisioningService->stopInstance($service->contabo_instance_id);
                break;
                
            case 'restart':
                $result = $provisioningService->rebootInstance($service->contabo_instance_id);
                break;
                
            case 'reinstall':
                $result = $provisioningService->reinstallInstance($service->contabo_instance_id, $this->payload);
                if ($result->success) {
                    $service->update([
                        'encrypted_credentials' => encrypt($result->data['initialPassword'] ?? ''),
                    ]);
                }
                break;
        }
        
        if ($result && !$result->success) {
            throw new \Exception('Provisioning action failed: ' . $result->message);
        }
    }
}
