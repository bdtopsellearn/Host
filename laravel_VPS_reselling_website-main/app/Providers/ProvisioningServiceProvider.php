<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Provisioning\ProvisioningServiceInterface;
use App\Services\Provisioning\ContaboProvisioningService;
use App\Services\Provisioning\MockProvisioningService;

class ProvisioningServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ProvisioningServiceInterface::class, function ($app) {
            $mode = strtolower((string) config('services.provisioning.mode', 'mock'));
            
            if (in_array($mode, ['contabo', 'live', 'real'])) {
                return new ContaboProvisioningService();
            }
            
            return new MockProvisioningService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
