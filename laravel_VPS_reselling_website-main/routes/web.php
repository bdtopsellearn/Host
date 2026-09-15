<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeWebhookController;

// Public & Marketing Routes (Protected against aggressive scraping & DoS via throttle:public)
Route::get('/login', function () {
    return redirect('/customer/login');
})->name('login');

Route::middleware('throttle:public')->group(function () {
    Route::get('/', function () {
        return view('home');
    });

    Route::get('/plans', function () {
        return view('plans');
    });

    Route::get('/privacy-policy', function () {
        return view('legal.privacy');
    })->name('legal.privacy');

    Route::get('/terms-of-service', function () {
        return view('legal.terms');
    })->name('legal.terms');

    Route::get('/cookie-policy', function () {
        return view('legal.cookies');
    })->name('legal.cookies');

    Route::get('/sitemap.xml', function () {
        $xmlContent = \Illuminate\Support\Facades\Cache::remember('catalog:sitemap_xml', 86400, function () {
            $packages = \App\Models\Package::getCachedActivePackages();
            return view('sitemap', compact('packages'))->render();
        });

        return response($xmlContent, 200, [
            'Content-Type' => 'text/xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    })->name('sitemap');
});

// Checkout Flow with Granular Throttling
Route::get('/checkout/{package}', [CheckoutController::class, 'show'])
    ->middleware('throttle:public')
    ->name('checkout.show');

Route::post('/checkout/{package}/configure', [CheckoutController::class, 'configure'])
    ->middleware('throttle:configure')
    ->name('checkout.configure');

Route::get('/checkout/{package}/payment', [CheckoutController::class, 'showPayment'])
    ->middleware(['auth', 'throttle:public'])
    ->name('checkout.payment');

Route::post('/checkout/{package}/payment', [CheckoutController::class, 'processPayment'])
    ->middleware(['auth', 'throttle:payment'])
    ->name('checkout.process');

Route::get('/checkout/invoice/{invoice}/crypto-pay', [CheckoutController::class, 'showCryptoPayment'])
    ->middleware(['auth', 'throttle:public'])
    ->name('checkout.crypto-pay');

Route::post('/checkout/invoice/{invoice}/crypto-txid', [CheckoutController::class, 'submitCryptoTxid'])
    ->middleware(['auth', 'throttle:crypto-txid'])
    ->name('checkout.crypto-submit-txid');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook');

Route::get('/customer/invoices/{invoice}/print', function (\App\Models\Invoice $invoice) {
    abort_unless(auth()->check() && (auth()->id() === $invoice->user_id || (auth()->user()->is_admin ?? false)), 403);
    return view('customer.invoice-print', ['invoice' => $invoice]);
})->middleware('auth')->name('customer.invoices.print');

// Deep Health Check Endpoint for Load Balancers (AWS ALB, Cloudflare, NGINX)
Route::get('/healthz', function () {
    $dbHealthy = false;
    $cacheHealthy = false;
    $errors = [];

    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $dbHealthy = true;
    } catch (\Throwable $e) {
        $errors['database'] = $e->getMessage();
    }

    try {
        \Illuminate\Support\Facades\Cache::put('healthz_probe', true, 10);
        $cacheHealthy = \Illuminate\Support\Facades\Cache::get('healthz_probe') === true;
    } catch (\Throwable $e) {
        $errors['cache'] = $e->getMessage();
    }

    $isHealthy = $dbHealthy && $cacheHealthy;
    $statusCode = $isHealthy ? 200 : 503;

    return response()->json([
        'status' => $isHealthy ? 'healthy' : 'unhealthy',
        'timestamp' => now()->toIso8601String(),
        'services' => [
            'database' => $dbHealthy ? 'connected' : 'disconnected',
            'cache' => $cacheHealthy ? 'connected' : 'disconnected',
        ],
        'errors' => empty($errors) ? null : $errors,
    ], $statusCode);
})->name('healthz');


