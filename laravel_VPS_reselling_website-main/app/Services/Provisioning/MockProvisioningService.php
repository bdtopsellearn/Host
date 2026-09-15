<?php

namespace App\Services\Provisioning;

use Illuminate\Support\Str;

class MockProvisioningService implements ProvisioningServiceInterface
{
    public function createInstance(array $orderData): ProvisioningResult
    {
        $instanceId = 'mock_contabo_' . rand(100000, 999999);
        $randomIp = '194.163.' . rand(10, 250) . '.' . rand(10, 250);
        $defaultUser = $orderData['default_user'] ?? 'root';
        $rootPassword = $orderData['root_password'] ?? (Str::password(14, true, true, false, false) . 'A1!');

        $result = ProvisioningResult::success([
            'instanceId' => $instanceId,
            'ipAddress' => $randomIp,
            'initialPassword' => $rootPassword,
            'defaultUser' => $defaultUser,
            'displayName' => $orderData['display_name'] ?? 'Mock Server',
            'region' => $orderData['region'] ?? 'EU',
            'productId' => $orderData['product_id'] ?? 'V153',
            'imageId' => $orderData['image_id'] ?? 'Ubuntu-22.04',
        ], 'Mock instance created successfully.');

        \App\Models\ProvisioningLog::create([
            'service_id' => $orderData['service_id'] ?? null,
            'action' => 'create_instance',
            'request_payload' => $orderData,
            'response_payload' => $result->data,
            'is_success' => true,
        ]);

        return $result;
    }

    public function getInstance(string $instanceId): ProvisioningResult
    {
        return ProvisioningResult::success([
            'instanceId' => $instanceId,
            'name' => 'vmd' . rand(10000, 99999),
            'displayName' => 'Cloud VPS Server',
            'status' => 'running',
            'ipAddress' => '194.163.140.' . (intval(substr(md5($instanceId), 0, 2), 16) % 200 + 10),
            'gateway' => '194.163.140.1',
            'netmaskCidr' => 24,
            'macAddress' => '00:50:56:' . strtoupper(substr(md5($instanceId), 0, 2)) . ':' . strtoupper(substr(md5($instanceId), 2, 2)) . ':01',
            'ramMb' => 8192,
            'cpuCores' => 4,
            'diskMb' => 102400,
            'osType' => 'Linux',
            'region' => 'EU',
            'regionName' => 'European Union (Germany)',
            'dataCenter' => 'European Union 1',
            'vHostName' => 'm1042',
            'createdDate' => now()->toISOString(),
        ], 'Mock instance details retrieved.');
    }

    public function getInstanceStatus(string $instanceId): ProvisioningResult
    {
        return ProvisioningResult::success([
            'status' => 'running',
            'ipAddress' => '194.163.140.42',
        ], 'Mock instance is running.');
    }

    public function startInstance(string $instanceId): ProvisioningResult
    {
        return ProvisioningResult::success([], 'Mock instance started.');
    }

    public function stopInstance(string $instanceId): ProvisioningResult
    {
        return ProvisioningResult::success([], 'Mock instance stopped.');
    }

    public function shutdownInstance(string $instanceId): ProvisioningResult
    {
        return ProvisioningResult::success([], 'Mock instance shut down.');
    }

    public function rebootInstance(string $instanceId): ProvisioningResult
    {
        return ProvisioningResult::success([], 'Mock instance rebooted.');
    }

    public function resetPassword(string $instanceId, string $newPassword): ProvisioningResult
    {
        return ProvisioningResult::success([
            'newPassword' => $newPassword,
        ], 'Mock root password reset successfully.');
    }

    public function rescueInstance(string $instanceId, ?string $rootPassword = null): ProvisioningResult
    {
        $pass = $rootPassword ?? (Str::password(14, true, true, false, false) . 'A1!');
        return ProvisioningResult::success([
            'rescuePassword' => $pass,
        ], 'Mock server booted into rescue mode.');
    }

    public function reinstallInstance(string $instanceId, array $options): ProvisioningResult
    {
        return ProvisioningResult::success([
            'initialPassword' => $options['password'] ?? (Str::password(14, true, true, false, false) . 'A1!'),
            'defaultUser' => $options['default_user'] ?? 'root',
        ], 'Mock instance reinstalled successfully.');
    }

    public function cancelInstance(string $instanceId): ProvisioningResult
    {
        return ProvisioningResult::success([], 'Mock instance cancelled.');
    }
}
