<?php

namespace App\Services\Provisioning;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\ProvisioningLog;
use Exception;

class ContaboProvisioningService implements ProvisioningServiceInterface
{
    protected string $baseUrl;
    protected string $authUrl;
    protected ?string $clientId;
    protected ?string $clientSecret;
    protected ?string $apiUser;
    protected ?string $apiPassword;
    protected string $defaultRegion;
    protected string $defaultImageId;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.contabo.base_url', 'https://api.contabo.com'), '/');
        $this->authUrl = config('services.contabo.auth_url', 'https://auth.contabo.com/auth/realms/contabo/protocol/openid-connect/token');
        $this->clientId = config('services.contabo.client_id');
        $this->clientSecret = config('services.contabo.client_secret');
        $this->apiUser = config('services.contabo.api_user');
        $this->apiPassword = config('services.contabo.api_password');
        $this->defaultRegion = config('services.contabo.default_region', 'EU');
        $this->defaultImageId = config('services.contabo.default_image_id', 'afecbb85-e2fc-46f0-9684-b46b1faf00bb');
    }

    /**
     * Retrieve OAuth2 Bearer Access Token with Cache.
     */
    public function getAccessToken(): string
    {
        if (empty($this->clientId) || empty($this->clientSecret) || empty($this->apiUser) || empty($this->apiPassword)) {
            throw new Exception('Contabo API credentials are not fully configured in your .env file (CONTABO_CLIENT_ID, CONTABO_CLIENT_SECRET, CONTABO_API_USER, CONTABO_API_PASSWORD).');
        }

        return Cache::remember('contabo_oauth_access_token', 280, function () {
            $http = Http::asForm()->timeout(15);
            if (app()->isLocal()) {
                $http = $http->withoutVerifying();
            }

            $response = $http->post($this->authUrl, [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'username' => $this->apiUser,
                'password' => $this->apiPassword,
                'grant_type' => 'password',
            ]);

            if ($response->failed()) {
                $errorMsg = $response->json('error_description') ?? $response->json('error') ?? $response->body();
                throw new Exception('Failed to authenticate with Contabo OpenID OAuth2: ' . $errorMsg);
            }

            $token = $response->json('access_token');
            if (empty($token)) {
                throw new Exception('No access_token returned by Contabo OAuth2 server.');
            }

            return $token;
        });
    }

    /**
     * Make an authenticated HTTP request to Contabo REST API.
     */
    protected function request(string $method, string $endpoint, array $data = [], ?int $serviceId = null, string $action = '')
    {
        try {
            $token = $this->getAccessToken();
            $url = $this->baseUrl . '/v1/' . ltrim($endpoint, '/');
            $requestId = (string) Str::uuid();

            $http = Http::withToken($token)
                ->acceptJson()
                ->withHeaders([
                    'x-request-id' => $requestId,
                    'Content-Type' => 'application/json',
                ])
                ->timeout(30);

            if (app()->isLocal()) {
                $http = $http->withoutVerifying();
            }

            $method = strtolower($method);

            if ($method === 'get') {
                $response = $http->get($url, $data);
            } elseif ($method === 'post') {
                $response = $http->post($url, $data);
            } elseif ($method === 'put') {
                $response = $http->put($url, $data);
            } elseif ($method === 'patch') {
                $response = $http->patch($url, $data);
            } elseif ($method === 'delete') {
                $response = $http->delete($url, $data);
            } else {
                throw new Exception("Unsupported HTTP method: {$method}");
            }

            // Log request & response
            $this->logProvisioning($serviceId, $action, $data, $response->json() ?? ['raw' => $response->body()], $response->successful());

            return $response;
        } catch (\Exception $e) {
            $this->logProvisioning($serviceId, $action, $data, ['error' => $e->getMessage()], false);
            throw $e;
        }
    }

    /**
     * Log provisioning actions to database.
     */
    protected function logProvisioning(?int $serviceId, string $action, array $request, ?array $response, bool $isSuccess): void
    {
        // Sanitize password from logs
        if (isset($request['rootPassword'])) {
            $request['rootPassword'] = '***';
        }
        if (isset($request['password'])) {
            $request['password'] = '***';
        }

        ProvisioningLog::create([
            'service_id' => $serviceId,
            'action' => $action,
            'request_payload' => $request,
            'response_payload' => $response,
            'is_success' => $isSuccess,
        ]);
    }

    /**
     * Create a new VPS instance on Contabo.
     */
    public function createInstance(array $orderData): ProvisioningResult
    {
        try {
            $serviceId = $orderData['service_id'] ?? null;
            $productId = $orderData['product_id'] ?? $orderData['contabo_product_id'] ?? 'V153';
            $imageId = $orderData['image_id'] ?? $this->defaultImageId;
            $region = $orderData['region'] ?? $this->defaultRegion;
            $period = $orderData['period'] ?? 1;
            $displayName = $orderData['display_name'] ?? ('VPS-' . strtoupper(Str::random(6)));
            $defaultUser = $orderData['default_user'] ?? 'root';
            $rootPassword = $orderData['root_password'] ?? Str::password(16, true, true, false, false) . 'A1!';

            // Generate cloud-init user_data to guarantee the root password is set on boot
            $userData = "#cloud-config\n"
                . "user: {$defaultUser}\n"
                . "ssh_pwauth: true\n"
                . "disable_root: false\n"
                . "chpasswd:\n"
                . "  list:\n"
                . "    - {$defaultUser}:{$rootPassword}\n"
                . "  expire: False\n";

            $payload = [
                'productId' => $productId,
                'imageId' => $imageId,
                'region' => $region,
                'period' => (int) $period,
                'displayName' => $displayName,
                'defaultUser' => $defaultUser,
                'userData' => $userData,
            ];

            $response = $this->request('post', 'compute/instances', $payload, $serviceId, 'create_instance');

            if ($response->successful()) {
                $data = $response->json('data')[0] ?? [];
                $instanceId = (string) ($data['instanceId'] ?? '');
                $ipAddress = $data['ipConfig']['v4']['ip'] ?? '';

                // If IP is not immediately in create response, fetch instance details
                if (empty($ipAddress) && !empty($instanceId)) {
                    sleep(2);
                    $detailsResult = $this->getInstance($instanceId);
                    if ($detailsResult->success) {
                        $ipAddress = $detailsResult->data['ipAddress'] ?? '';
                    }
                }

                return ProvisioningResult::success([
                    'instanceId' => $instanceId,
                    'ipAddress' => $ipAddress,
                    'initialPassword' => $rootPassword,
                    'defaultUser' => $defaultUser,
                    'displayName' => $displayName,
                    'region' => $region,
                    'productId' => $productId,
                    'imageId' => $imageId,
                    'raw' => $data,
                ], 'Instance created successfully on Contabo.', $response->json());
            }

            $errorMsg = $response->json('message') ?? $response->json('errors')[0]['detail'] ?? $response->body();
            return ProvisioningResult::failure('Failed to create Contabo instance: ' . $errorMsg, $response->json());
        } catch (Exception $e) {
            return ProvisioningResult::failure('Contabo API Exception: ' . $e->getMessage());
        }
    }

    /**
     * Get details of a specific instance.
     */
    public function getInstance(string $instanceId): ProvisioningResult
    {
        try {
            $response = $this->request('get', "compute/instances/{$instanceId}", [], null, 'get_instance');

            if ($response->successful()) {
                $data = $response->json('data')[0] ?? [];
                $ipAddress = $data['ipConfig']['v4']['ip'] ?? ($data['additionalIps'][0]['v4']['ip'] ?? 'Pending Assignment');

                return ProvisioningResult::success([
                    'instanceId' => (string) ($data['instanceId'] ?? $instanceId),
                    'name' => $data['name'] ?? '',
                    'displayName' => $data['displayName'] ?? '',
                    'status' => $data['status'] ?? 'unknown',
                    'ipAddress' => $ipAddress,
                    'gateway' => $data['ipConfig']['v4']['gateway'] ?? '',
                    'netmaskCidr' => $data['ipConfig']['v4']['netmaskCidr'] ?? '',
                    'macAddress' => $data['macAddress'] ?? '',
                    'ramMb' => $data['ramMb'] ?? 0,
                    'cpuCores' => $data['cpuCores'] ?? 0,
                    'diskMb' => $data['diskMb'] ?? 0,
                    'osType' => $data['osType'] ?? 'Linux',
                    'region' => $data['region'] ?? '',
                    'regionName' => $data['regionName'] ?? '',
                    'dataCenter' => $data['dataCenter'] ?? '',
                    'vHostName' => $data['vHostName'] ?? '',
                    'createdDate' => $data['createdDate'] ?? '',
                    'raw' => $data,
                ], 'Instance details retrieved successfully.', $response->json());
            }

            return ProvisioningResult::failure('Failed to retrieve instance details from Contabo.', $response->json());
        } catch (Exception $e) {
            return ProvisioningResult::failure('Contabo API Exception: ' . $e->getMessage());
        }
    }

    /**
     * Get real-time status of a specific instance.
     */
    public function getInstanceStatus(string $instanceId): ProvisioningResult
    {
        $instanceResult = $this->getInstance($instanceId);
        if ($instanceResult->success) {
            return ProvisioningResult::success([
                'status' => $instanceResult->data['status'] ?? 'unknown',
                'ipAddress' => $instanceResult->data['ipAddress'] ?? '',
            ], 'Status retrieved.');
        }

        return $instanceResult;
    }

    /**
     * Start/Power-on a specific instance.
     */
    public function startInstance(string $instanceId): ProvisioningResult
    {
        try {
            $response = $this->request('post', "compute/instances/{$instanceId}/actions/start", [], null, 'start_instance');

            if ($response->successful()) {
                return ProvisioningResult::success([], 'Server started successfully.', $response->json());
            }

            $errorMsg = $response->json('message') ?? $response->body();
            return ProvisioningResult::failure('Failed to start instance: ' . $errorMsg, $response->json());
        } catch (Exception $e) {
            return ProvisioningResult::failure('Contabo API Exception: ' . $e->getMessage());
        }
    }

    /**
     * Stop/Power-off a specific instance.
     */
    public function stopInstance(string $instanceId): ProvisioningResult
    {
        try {
            $response = $this->request('post', "compute/instances/{$instanceId}/actions/stop", [], null, 'stop_instance');

            if ($response->successful()) {
                return ProvisioningResult::success([], 'Server stopped successfully.', $response->json());
            }

            $errorMsg = $response->json('message') ?? $response->body();
            return ProvisioningResult::failure('Failed to stop instance: ' . $errorMsg, $response->json());
        } catch (Exception $e) {
            return ProvisioningResult::failure('Contabo API Exception: ' . $e->getMessage());
        }
    }

    /**
     * Clean ACPI shutdown of a specific instance.
     */
    public function shutdownInstance(string $instanceId): ProvisioningResult
    {
        try {
            $response = $this->request('post', "compute/instances/{$instanceId}/actions/shutdown", [], null, 'shutdown_instance');

            if ($response->successful()) {
                return ProvisioningResult::success([], 'Server shutdown signal sent successfully.', $response->json());
            }

            $errorMsg = $response->json('message') ?? $response->body();
            return ProvisioningResult::failure('Failed to shutdown instance: ' . $errorMsg, $response->json());
        } catch (Exception $e) {
            return ProvisioningResult::failure('Contabo API Exception: ' . $e->getMessage());
        }
    }

    /**
     * Reboot/Restart a specific instance.
     */
    public function rebootInstance(string $instanceId): ProvisioningResult
    {
        try {
            $response = $this->request('post', "compute/instances/{$instanceId}/actions/restart", [], null, 'reboot_instance');

            if ($response->successful()) {
                return ProvisioningResult::success([], 'Server rebooted successfully.', $response->json());
            }

            $errorMsg = $response->json('message') ?? $response->body();
            return ProvisioningResult::failure('Failed to reboot instance: ' . $errorMsg, $response->json());
        } catch (Exception $e) {
            return ProvisioningResult::failure('Contabo API Exception: ' . $e->getMessage());
        }
    }

    /**
     * Reset the root/admin password of a specific instance.
     */
    public function resetPassword(string $instanceId, string $newPassword): ProvisioningResult
    {
        try {
            $userData = "#cloud-config\n"
                . "user: root\n"
                . "ssh_pwauth: true\n"
                . "disable_root: false\n"
                . "chpasswd:\n"
                . "  list:\n"
                . "    - root:{$newPassword}\n"
                . "  expire: False\n";

            $payload = [
                'userData' => $userData,
            ];

            $response = $this->request('post', "compute/instances/{$instanceId}/actions/resetPassword", $payload, null, 'reset_password');

            if ($response->successful()) {
                return ProvisioningResult::success([
                    'newPassword' => $newPassword,
                ], 'Password reset signal sent successfully.', $response->json());
            }

            $errorMsg = $response->json('message') ?? $response->body();
            return ProvisioningResult::failure('Failed to reset password: ' . $errorMsg, $response->json());
        } catch (Exception $e) {
            return ProvisioningResult::failure('Contabo API Exception: ' . $e->getMessage());
        }
    }

    /**
     * Boot the instance into rescue mode.
     */
    public function rescueInstance(string $instanceId, ?string $rootPassword = null): ProvisioningResult
    {
        try {
            $pass = $rootPassword ?? Str::password(16, true, true, false, false) . 'A1!';
            $userData = "#cloud-config\n"
                . "user: root\n"
                . "ssh_pwauth: true\n"
                . "disable_root: false\n"
                . "chpasswd:\n"
                . "  list:\n"
                . "    - root:{$pass}\n"
                . "  expire: False\n";

            $payload = [
                'userData' => $userData,
            ];

            $response = $this->request('post', "compute/instances/{$instanceId}/actions/rescue", $payload, null, 'rescue_instance');

            if ($response->successful()) {
                return ProvisioningResult::success([
                    'rescuePassword' => $pass,
                ], 'Server booted into rescue mode successfully.', $response->json());
            }

            $errorMsg = $response->json('message') ?? $response->body();
            return ProvisioningResult::failure('Failed to boot into rescue mode: ' . $errorMsg, $response->json());
        } catch (Exception $e) {
            return ProvisioningResult::failure('Contabo API Exception: ' . $e->getMessage());
        }
    }

    /**
     * Reinstall a specific instance with a new OS image.
     */
    public function reinstallInstance(string $instanceId, array $options): ProvisioningResult
    {
        try {
            $imageId = $options['image_id'] ?? $options['imageId'] ?? $this->defaultImageId;
            $defaultUser = $options['default_user'] ?? 'root';
            $password = $options['password'] ?? $options['root_password'] ?? (Str::password(16, true, true, false, false) . 'A1!');

            $userData = "#cloud-config\n"
                . "user: {$defaultUser}\n"
                . "ssh_pwauth: true\n"
                . "disable_root: false\n"
                . "chpasswd:\n"
                . "  list:\n"
                . "    - {$defaultUser}:{$password}\n"
                . "  expire: False\n";

            $payload = [
                'imageId' => $imageId,
                'defaultUser' => $defaultUser,
                'userData' => $userData,
            ];

            $response = $this->request('put', "compute/instances/{$instanceId}", $payload, null, 'reinstall_instance');

            if ($response->successful()) {
                return ProvisioningResult::success([
                    'initialPassword' => $password,
                    'defaultUser' => $defaultUser,
                    'imageId' => $imageId,
                ], 'Instance OS reinstallation initiated successfully.', $response->json());
            }

            $errorMsg = $response->json('message') ?? $response->body();
            return ProvisioningResult::failure('Failed to reinstall instance: ' . $errorMsg, $response->json());
        } catch (Exception $e) {
            return ProvisioningResult::failure('Contabo API Exception: ' . $e->getMessage());
        }
    }

    /**
     * Cancel an instance on Contabo.
     */
    public function cancelInstance(string $instanceId): ProvisioningResult
    {
        try {
            $response = $this->request('post', "compute/instances/{$instanceId}/cancel", [], null, 'cancel_instance');

            if ($response->successful()) {
                return ProvisioningResult::success([], 'Instance cancelled successfully.', $response->json());
            }

            $errorMsg = $response->json('message') ?? $response->body();
            return ProvisioningResult::failure('Failed to cancel instance: ' . $errorMsg, $response->json());
        } catch (Exception $e) {
            return ProvisioningResult::failure('Contabo API Exception: ' . $e->getMessage());
        }
    }
}
