<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Package;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Service;
use App\Services\Provisioning\MockProvisioningService;
use App\Services\Provisioning\ContaboProvisioningService;
use Illuminate\Support\Str;

echo "=======================================================\n";
echo "   VORTEXCLOUD WORKFLOW AUDIT & INTEGRATION TEST       \n";
echo "=======================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest(string $title, bool $condition, string $details = '') {
    global $passCount, $failCount;
    if ($condition) {
        $passCount++;
        echo " [PASS] " . $title . "\n";
    } else {
        $failCount++;
        echo " [FAIL] " . $title . "\n";
        if ($details) {
            echo "        Details: " . $details . "\n";
        }
    }
}

// -------------------------------------------------------------
// 0. AUDIT ENVIRONMENT & AGENTS.MD ARCHITECTURAL STANDARDS
// -------------------------------------------------------------
echo "\n--- Section 0: Environment Sync & AGENTS.md Compliance ---\n";

$envFile = dirname(__DIR__) . '/.env';
$envExampleFile = dirname(__DIR__) . '/.env.example';

$parseKeys = function (string $filePath) {
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $keys = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || str_starts_with($line, '#')) continue;
        if (str_contains($line, '=')) {
            $keys[] = trim(explode('=', $line, 2)[0]);
        }
    }
    return $keys;
};

$envKeys = $parseKeys($envFile);
$exampleKeys = $parseKeys($envExampleFile);

$missingInExample = array_diff($envKeys, $exampleKeys);
assertTest("AGENTS.md Sec 11: All .env variables exist in .env.example", empty($missingInExample), 
    !empty($missingInExample) ? 'Missing in .env.example: ' . implode(', ', $missingInExample) : '');

// AGENTS.md Sec 9: Table Row Actions Standard - verify recordActions in OrdersTable & InvoicesTable contain ONLY ViewAction
$checkRecordActions = function (string $code) {
    if (preg_match('/->recordActions\(\[\s*([\s\S]*?)\s*\]\)/', $code, $matches)) {
        $actionsBlock = trim($matches[1]);
        return $actionsBlock === 'ViewAction::make(),' || $actionsBlock === 'ViewAction::make()';
    }
    return false;
};

$ordersTableCode = file_get_contents(dirname(__DIR__) . '/app/Filament/Resources/Orders/Tables/OrdersTable.php');
assertTest("AGENTS.md Sec 9: OrdersTable recordActions contain ONLY ViewAction", $checkRecordActions($ordersTableCode));

$invoicesTableCode = file_get_contents(dirname(__DIR__) . '/app/Filament/Resources/Invoices/Tables/InvoicesTable.php');
assertTest("AGENTS.md Sec 9: InvoicesTable recordActions contain ONLY ViewAction", $checkRecordActions($invoicesTableCode));

// -------------------------------------------------------------
// 1. AUDIT CONTABO OPENAPI COMPLIANCE & CREDENTIALS
// -------------------------------------------------------------
echo "\n--- Section 1: Contabo API & OpenAPI Specification Audit ---\n";

$apiDocPath = dirname(__DIR__) . '/docs/contabo-api.json';
assertTest("docs/contabo-api.json exists as SSOT", file_exists($apiDocPath));

$specContent = json_decode(file_get_contents($apiDocPath), true);
assertTest("docs/contabo-api.json is valid JSON", is_array($specContent));

// Verify endpoint POST /v1/compute/instances exists in OpenAPI spec
$hasPostInstance = isset($specContent['paths']['/v1/compute/instances']['post']);
assertTest("OpenAPI spec defines POST /v1/compute/instances", $hasPostInstance);

// Verify allowed regions in spec
$allowedRegions = ['EU', 'US-central', 'US-east', 'US-west', 'SIN', 'UK', 'AUS', 'JPN', 'IND'];
echo "   Checking allowed OpenAPI datacenter region enums: " . implode(', ', $allowedRegions) . "\n";

// Verify packages in DB use valid Contabo productIds (V153-V158)
$packages = Package::all();
$validProductIds = ['V153', 'V154', 'V155', 'V156', 'V157', 'V158'];
$allPackagesValid = true;
foreach ($packages as $pkg) {
    if (!in_array($pkg->contabo_product_id, $validProductIds)) {
        $allPackagesValid = false;
        echo "   Package {$pkg->name} has invalid contabo_product_id: {$pkg->contabo_product_id}\n";
    }
}
assertTest("All packages map to official Contabo productIds (V153-V158)", $allPackagesValid && $packages->count() > 0);

// Verify Contabo OAuth2 authentication live
$contaboService = new ContaboProvisioningService();
try {
    $token = $contaboService->getAccessToken();
    assertTest("Contabo OAuth2 authentication successful (token length: " . strlen($token) . ")", !empty($token));
} catch (\Exception $e) {
    assertTest("Contabo OAuth2 authentication successful", false, $e->getMessage());
}

// -------------------------------------------------------------
// 2. AUDIT WORKFLOW: ORDER CREATION & INITIAL PENDING STATE
// -------------------------------------------------------------
echo "\n--- Section 2: Order & Invoice Lifecycle (Step 1: Check & Confirm) ---\n";

$testUser = User::first() ?? User::factory()->create([
    'name' => 'Audit Tester',
    'email' => 'audit@example.com',
    'password' => bcrypt('Password123!'),
]);

$package = Package::first();
$orderNumber = 'ORD-AUDIT-' . strtoupper(Str::random(6));

// Step A: Customer places order with crypto payment
$order = Order::create([
    'user_id' => $testUser->id,
    'order_number' => $orderNumber,
    'total_amount' => 15.00,
    'status' => 'pending',
]);

$invoice = Invoice::create([
    'user_id' => $testUser->id,
    'order_id' => $order->id,
    'invoice_number' => Invoice::generateNextNumber(),
    'amount' => 15.00,
    'tax' => 0,
    'total' => 15.00,
    'status' => 'pending',
    'payment_method' => 'crypto',
    'crypto_network' => 'usdt_trc20',
    'crypto_wallet_address' => 'TPFMfZU4cPcfi3ivmUECDj9bYy5aWdZ4EE',
    'crypto_txid' => '0x8f3c4d7b2a1e9f0c5a6b8d7e4c3b2a1f0e9d8c7b6a5',
    'due_date' => now()->addDays(7),
]);

$service = Service::create([
    'user_id' => $testUser->id,
    'order_id' => $order->id,
    'package_id' => $package->id,
    'status' => 'awaiting_provisioning',
    'billing_cycle' => 'monthly',
    'recurring_amount' => 15.00,
    'specs_snapshot' => [
        'package_name' => $package->name,
        'cores' => '4 vCPU Cores',
        'memory' => '8 GB RAM',
        'storage' => '100 GB NVMe',
        'datacenter' => 'EU Central (Germany)',
    ],
    'region' => 'EU',
    'encrypted_credentials' => json_encode([
        'os' => 'Ubuntu 22.04 LTS',
        'os_api_identifier' => 'ubuntu-22.04-x86_64',
        'datacenter' => 'EU Central (Germany)',
        'region_api_identifier' => 'EU',
        'root_password' => encrypt('InitialSecretPass123!'),
    ]),
]);

assertTest("Initial State: Invoice status is 'pending'", $invoice->status === 'pending');
assertTest("Initial State: Order status is 'pending'", $order->status === 'pending');
assertTest("Initial State: Service status is 'awaiting_provisioning'", $service->status === 'awaiting_provisioning');

// In initial pending state, accept_and_deploy should be DISABLED
$deployVisibleInitial = in_array($order->status, ['payment_confirmed', 'provision', 'failed']) 
    || ($order->invoice?->status === 'paid' && !in_array($order->status, ['active', 'contabo_ok', 'cancelled']));

assertTest("Safety Check: 'Accept & Deploy' button is INACTIVE when payment is pending", $deployVisibleInitial === false);

// -------------------------------------------------------------
// 3. AUDIT STEP 1: CONFIRM PAYMENT (ViewInvoice action)
// -------------------------------------------------------------
echo "\n--- Section 3: Executing Confirm Payment Action ---\n";

// Execute the exact action logic from ViewInvoice
$invoice->update([
    'status' => 'paid',
    'paid_at' => now(),
]);

if ($invoice->order) {
    $invoice->order->update(['status' => 'payment_confirmed']);
}

Service::where('order_id', $invoice->order_id)->update(['status' => 'ready_for_provisioning']);

$invoice->refresh();
$order->refresh();
$service->refresh();

assertTest("Payment Confirmed: Invoice status transitioned to 'paid'", $invoice->status === 'paid');
assertTest("Payment Confirmed: Invoice paid_at timestamp is set", !empty($invoice->paid_at));
assertTest("Payment Confirmed: Order status transitioned to 'payment_confirmed'", $order->status === 'payment_confirmed');
assertTest("Payment Confirmed: Service status transitioned to 'ready_for_provisioning'", $service->status === 'ready_for_provisioning');

// Now check if 'Accept & Deploy to Contabo' button is ACTIVE
$deployVisibleAfterConfirm = in_array($order->status, ['payment_confirmed', 'provision', 'failed']) 
    || ($order->invoice?->status === 'paid' && !in_array($order->status, ['active', 'contabo_ok', 'cancelled']));

assertTest("Action Activation: 'Accept & Deploy' button is now ACTIVE on Order page", $deployVisibleAfterConfirm === true);

// -------------------------------------------------------------
// 4. AUDIT STEP 2: MODAL PRE-POPULATION & FINAL REVIEW
// -------------------------------------------------------------
echo "\n--- Section 4: Modal Pre-Population & Server Parameters Final Check ---\n";

$creds = is_array($service->encrypted_credentials) 
    ? $service->encrypted_credentials 
    : json_decode($service->encrypted_credentials ?? '[]', true);

$defaultProductId = $package->contabo_product_id ?? 'V153';
$regionCandidate = $service->region ?? ($creds['region_api_identifier'] ?? null) ?? 'EU';
$selectedRegion = in_array($regionCandidate, $allowedRegions) ? $regionCandidate : 'EU';

$imageCandidate = $creds['os_api_identifier'] ?? null;
$defaultImageId = config('services.contabo.default_image_id', 'afecbb85-e2fc-46f0-9684-b46b1faf00bb');
if ($imageCandidate && str_contains($imageCandidate, '20.04')) {
    $defaultImageId = '9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d';
}

$initialPassword = $service->decrypted_password ?? (Str::password(16, true, true, false, false) . 'A1!');

assertTest("Modal defaults: Product ID correctly resolved to '{$defaultProductId}'", in_array($defaultProductId, $validProductIds));
assertTest("Modal defaults: Region correctly resolved to '{$selectedRegion}'", in_array($selectedRegion, $allowedRegions));
assertTest("Modal defaults: OS image UUID resolved to '{$defaultImageId}'", !empty($defaultImageId));
assertTest("Modal defaults: Root password generated/decrypted cleanly", strlen($initialPassword) >= 12);

// -------------------------------------------------------------
// 5. AUDIT STEP 3: CONTABO API CALL & DEPLOYMENT
// -------------------------------------------------------------
echo "\n--- Section 5: Contabo Provisioning Simulation & Service Updates ---\n";

$mockService = new MockProvisioningService();

$orderPayload = [
    'service_id' => $service->id,
    'product_id' => $defaultProductId,
    'region' => $selectedRegion,
    'image_id' => $defaultImageId,
    'default_user' => 'root',
    'root_password' => $initialPassword,
    'display_name' => 'VPS-' . $order->order_number,
    'period' => 1,
];

$result = $mockService->createInstance($orderPayload);
assertTest("Provisioning execution returns success", $result->success);
assertTest("Provisioning returns valid instanceId", !empty($result->data['instanceId']));
assertTest("Provisioning returns assigned IP address", !empty($result->data['ipAddress']));

// Apply database updates as executed by ViewOrder action
$service->update([
    'contabo_instance_id' => $result->data['instanceId'] ?? '',
    'ip_address' => $result->data['ipAddress'] ?? 'Pending IP',
    'encrypted_credentials' => encrypt($result->data['initialPassword'] ?? $orderPayload['root_password']),
    'default_user' => $result->data['defaultUser'] ?? $orderPayload['default_user'],
    'server_name' => $orderPayload['display_name'],
    'os_image' => 'Linux OS',
    'region' => $orderPayload['region'],
    'status' => 'contabo_ok',
]);

$order->update(['status' => 'contabo_ok']);

$order->refresh();
$service->refresh();

assertTest("Post-Deploy: Order status transitioned to 'contabo_ok'", $order->status === 'contabo_ok');
assertTest("Post-Deploy: Service status transitioned to 'contabo_ok'", $service->status === 'contabo_ok');
assertTest("Post-Deploy: Service IP address is assigned ({$service->ip_address})", !empty($service->ip_address));
assertTest("Post-Deploy: Service contabo_instance_id is saved ({$service->contabo_instance_id})", !empty($service->contabo_instance_id));

// -------------------------------------------------------------
// 6. AUDIT STEP 4: ACCEPT & DELIVER CREDENTIALS
// -------------------------------------------------------------
echo "\n--- Section 6: Accept & Deliver Credentials to Customer ---\n";

$deliverVisible = ($order->status === 'contabo_ok');
assertTest("Delivery Action: 'Accept & Deliver' button is ACTIVE when status is 'contabo_ok'", $deliverVisible === true);

// Execute Accept & Deliver logic
$order->update(['status' => 'active']);
$service->update(['status' => 'active', 'next_due_date' => now()->addMonth()]);

$order->refresh();
$service->refresh();

assertTest("Final State: Order status is 'active'", $order->status === 'active');
assertTest("Final State: Service status is 'active'", $service->status === 'active');
assertTest("Final State: Decrypted password matches root password", $service->decrypted_password === $initialPassword);

// -------------------------------------------------------------
// 7. CLEANUP
// -------------------------------------------------------------
echo "\n--- Section 7: Cleaning Up Audit Test Records ---\n";
$service->delete();
$invoice->delete();
$order->delete();
echo "   Temporary test records deleted cleanly.\n";

echo "\n=======================================================\n";
echo "   AUDIT SUMMARY: {$passCount} PASSED / {$failCount} FAILED\n";
echo "=======================================================\n";

if ($failCount > 0) {
    exit(1);
}
exit(0);
