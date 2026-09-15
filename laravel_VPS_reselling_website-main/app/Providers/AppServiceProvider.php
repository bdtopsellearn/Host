<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Schema::defaultStringLength(125);

        if (!app()->runningInConsole()) {
            $host = request()->header('x-forwarded-host') ?? request()->header('host');
            if ($host && !str_contains($host, '127.0.0.1') && !str_contains($host, 'localhost')) {
                $proto = request()->header('x-forwarded-proto') ?? (str_contains($host, 'run.app') ? 'https' : 'http');
                \Illuminate\Support\Facades\URL::forceRootUrl("{$proto}://{$host}");
                \Illuminate\Support\Facades\URL::forceScheme($proto);
            } elseif (str_contains(config('app.url'), 'run.app')) {
                \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
                \Illuminate\Support\Facades\URL::forceScheme('https');
            }
        }

        $this->configureRedisFallback();
        $this->configureRateLimiting();
        $this->configureActivityLogging();
    }

    /**
     * Configure automatic customer activity logging for security, auditing, and WHMCS parity.
     */
    protected function configureActivityLogging(): void
    {
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function (\Illuminate\Auth\Events\Login $event) {
            if ($event->user instanceof \App\Models\User) {
                \App\Models\UserActivityLog::record([
                    'user_id' => $event->user->id,
                    'actor_type' => 'user',
                    'action' => 'LOGIN',
                    'description' => 'Customer logged into client portal',
                ]);
            } elseif ($event->user instanceof \App\Models\Admin) {
                \App\Models\UserActivityLog::record([
                    'admin_id' => $event->user->id,
                    'actor_type' => 'admin',
                    'action' => 'ADMIN_LOGIN',
                    'description' => "Administrator {$event->user->name} logged into Admin Console",
                ]);
            }
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Logout::class, function (\Illuminate\Auth\Events\Logout $event) {
            if ($event->user instanceof \App\Models\User) {
                \App\Models\UserActivityLog::record([
                    'user_id' => $event->user->id,
                    'actor_type' => 'user',
                    'action' => 'LOGOUT',
                    'description' => 'Customer logged out of client portal',
                ]);
            } elseif ($event->user instanceof \App\Models\Admin) {
                \App\Models\UserActivityLog::record([
                    'admin_id' => $event->user->id,
                    'actor_type' => 'admin',
                    'action' => 'ADMIN_LOGOUT',
                    'description' => "Administrator {$event->user->name} logged out of Admin Console",
                ]);
            }
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Failed::class, function (\Illuminate\Auth\Events\Failed $event) {
            $email = $event->credentials['email'] ?? 'Unknown Account';
            $guard = $event->guard ?? 'web';
            $target = $guard === 'admin' ? 'Admin Console' : 'Client Portal';

            \App\Models\UserActivityLog::record([
                'user_id' => $event->user?->id,
                'actor_type' => $guard === 'admin' ? 'admin' : 'user',
                'action' => 'FAILED_LOGIN',
                'description' => "Failed authentication attempt for '{$email}' on {$target}",
            ]);
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\PasswordReset::class, function (\Illuminate\Auth\Events\PasswordReset $event) {
            if ($event->user instanceof \App\Models\User) {
                \App\Models\UserActivityLog::record([
                    'user_id' => $event->user->id,
                    'actor_type' => 'user',
                    'action' => 'PASSWORD_RESET',
                    'description' => 'Customer successfully reset account password via recovery link',
                ]);
            }
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Registered::class, function (\Illuminate\Auth\Events\Registered $event) {
            if ($event->user instanceof \App\Models\User) {
                \App\Models\UserActivityLog::record([
                    'user_id' => $event->user->id,
                    'actor_type' => 'user',
                    'action' => 'REGISTERED',
                    'description' => 'New customer account registered',
                ]);
            }
        });
    }

    /**
     * Ensure session, cache, and queue gracefully fallback to database
     * if Redis is configured in .env but unreachable on the current host.
     */
    protected function configureRedisFallback(): void
    {
        $usesRedis = config('session.driver') === 'redis' 
            || config('cache.default') === 'redis' 
            || config('queue.default') === 'redis';

        if (!$usesRedis) {
            return;
        }

        try {
            $host = config('database.redis.default.host', '127.0.0.1');
            $port = (int) config('database.redis.default.port', 6379);

            // Fast socket probe (0.2s timeout)
            $connection = @fsockopen($host, $port, $errno, $errstr, 0.2);
            if (!$connection) {
                $this->applyDriverFallbacks();
            } else {
                fclose($connection);
            }
        } catch (\Throwable $e) {
            $this->applyDriverFallbacks();
        }
    }

    protected function applyDriverFallbacks(): void
    {
        if (config('session.driver') === 'redis') {
            config(['session.driver' => 'database']);
        }
        if (config('cache.default') === 'redis') {
            config(['cache.default' => 'database']);
        }
        if (config('queue.default') === 'redis') {
            config(['queue.default' => 'database']);
        }
    }

    /**
     * Configure application-wide named rate limiters.
     */
    protected function configureRateLimiting(): void
    {
        // 1. Universal Payment Processing Throttle (5 attempts per 10 mins per User/IP)
        // Protects Crypto TxID submissions now; stops card-testing later.
        RateLimiter::for('payment', function (Request $request) {
            $key = ($request->user()?->id ?: $request->ip());
            return Limit::perMinutes(10, 5)
                ->by($key)
                ->response(function (Request $request, array $headers) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'error' => 'Too many payment attempts. Please wait 10 minutes before trying again.',
                        ], 429, $headers);
                    }
                    return back()->with('error', 'Too many payment attempts. For your security, please wait a few minutes before trying again.')->withHeaders($headers);
                });
        });

        // 2. Crypto TxID Submission Throttle (5 submissions per 10 mins per Invoice/User/IP)
        // Prevents spamming false blockchain hashes to the verification queue.
        RateLimiter::for('crypto-txid', function (Request $request) {
            $invoiceId = $request->route('invoice')?->id ?? 'global';
            $userKey = $request->user()?->id ?: $request->ip();
            $key = "{$userKey}|{$invoiceId}";

            return Limit::perMinutes(10, 5)
                ->by($key)
                ->response(function (Request $request, array $headers) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'error' => 'Too many transaction hash submissions. Please wait before trying again.',
                        ], 429, $headers);
                    }
                    return back()->with('error', 'Too many transaction submissions. Please wait a few minutes before submitting another TxID.')->withHeaders($headers);
                });
        });

        // 3. Server Configuration & Coupon Code Throttle (15 attempts per minute per IP)
        // Stops coupon brute-force attacks and session flooding.
        RateLimiter::for('configure', function (Request $request) {
            return Limit::perMinute(15)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'error' => 'Too many configuration attempts. Please wait a moment.',
                        ], 429, $headers);
                    }
                    return back()->with('error', 'Too many requests. Please slow down and wait a moment.')->withHeaders($headers);
                });
        });

        // 4. Public Marketing & Catalog Browsing Throttle (60 requests per minute per IP)
        // Protects against aggressive scraping and DoS on homepage, plans, and sitemaps.
        RateLimiter::for('public', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // 5. Authentication & Registration Throttle (5 attempts per minute per Email + IP)
        // Defense against credential stuffing and brute-force registration scripts.
        RateLimiter::for('auth', function (Request $request) {
            $email = (string) $request->input('email', '');
            $key = Str::transliterate(Str::lower($email)) . '|' . $request->ip();
            return Limit::perMinute(5)
                ->by($key)
                ->response(function (Request $request, array $headers) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'error' => 'Too many authentication attempts. Please try again in 1 minute.',
                        ], 429, $headers);
                    }
                    return back()->with('error', 'Too many attempts. For your security, please wait 60 seconds before trying again.')->withHeaders($headers);
                });
        });
    }
}

