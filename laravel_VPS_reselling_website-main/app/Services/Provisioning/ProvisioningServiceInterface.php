<?php

namespace App\Services\Provisioning;

interface ProvisioningServiceInterface
{
    /**
     * Create a new VPS instance on Contabo or mock provider.
     */
    public function createInstance(array $orderData): ProvisioningResult;

    /**
     * Get details of a specific instance.
     */
    public function getInstance(string $instanceId): ProvisioningResult;

    /**
     * Get real-time status of a specific instance.
     */
    public function getInstanceStatus(string $instanceId): ProvisioningResult;

    /**
     * Start/Power-on a specific instance.
     */
    public function startInstance(string $instanceId): ProvisioningResult;

    /**
     * Stop/Power-off a specific instance.
     */
    public function stopInstance(string $instanceId): ProvisioningResult;

    /**
     * Graceful ACPI shutdown of a specific instance.
     */
    public function shutdownInstance(string $instanceId): ProvisioningResult;

    /**
     * Reboot/Restart a specific instance.
     */
    public function rebootInstance(string $instanceId): ProvisioningResult;

    /**
     * Reset the root/admin password of a specific instance.
     */
    public function resetPassword(string $instanceId, string $newPassword): ProvisioningResult;

    /**
     * Boot the instance into rescue mode.
     */
    public function rescueInstance(string $instanceId, ?string $rootPassword = null): ProvisioningResult;

    /**
     * Reinstall a specific instance with a new OS image.
     */
    public function reinstallInstance(string $instanceId, array $options): ProvisioningResult;

    /**
     * Cancel an instance.
     */
    public function cancelInstance(string $instanceId): ProvisioningResult;
}
