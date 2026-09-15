<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'actor_type',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'device',
        'browser',
        'platform',
        'request_method',
        'request_url',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Check if user_activity_logs table exists in the current database connection.
     */
    public static function tableExists(): bool
    {
        return Cache::remember('user_activity_logs_table_exists', 15, function () {
            try {
                return Schema::hasTable('user_activity_logs');
            } catch (\Throwable $e) {
                return false;
            }
        });
    }

    protected static function booted(): void
    {
        static::creating(function (UserActivityLog $log) {
            if (!self::tableExists()) {
                return false;
            }

            $request = request();
            $columns = self::getTableColumns();

            if (!empty($columns)) {
                if (in_array('ip_address', $columns) && empty($log->ip_address) && $request) {
                    $log->ip_address = $request->ip();
                }
                if (in_array('user_agent', $columns) && empty($log->user_agent) && $request?->userAgent()) {
                    $log->user_agent = substr($request->userAgent(), 0, 500);
                }
                if (in_array('request_method', $columns) && empty($log->request_method)) {
                    $log->request_method = strtoupper($request?->method() ?? 'CLI');
                }
                if (in_array('request_url', $columns) && empty($log->request_url) && $request) {
                    $log->request_url = substr($request->fullUrl(), 0, 500);
                }
                if (in_array('device', $columns) || in_array('browser', $columns) || in_array('platform', $columns)) {
                    $parsed = self::parseUserAgent($log->user_agent ?? $request?->userAgent());
                    if (in_array('device', $columns) && empty($log->device)) {
                        $log->device = $parsed['device'];
                    }
                    if (in_array('browser', $columns) && empty($log->browser)) {
                        $log->browser = $parsed['browser'];
                    }
                    if (in_array('platform', $columns) && empty($log->platform)) {
                        $log->platform = $parsed['platform'];
                    }
                }
                if (in_array('actor_type', $columns) && empty($log->actor_type)) {
                    $log->actor_type = $log->admin_id ? 'admin' : ($log->user_id ? 'user' : 'system');
                }
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Record a system or user activity log with automatic telemetry enrichment.
     */
    public static function record(array $attributes): self
    {
        if (!self::tableExists()) {
            return new self($attributes);
        }

        $request = request();
        $userAgent = $attributes['user_agent'] ?? ($request?->userAgent() ?? null);
        $ip = $attributes['ip_address'] ?? ($request?->ip() ?? null);
        $method = $attributes['request_method'] ?? ($request?->method() ?? null);
        $url = $attributes['request_url'] ?? ($request?->fullUrl() ?? null);

        $parsed = self::parseUserAgent($userAgent);

        $adminId = $attributes['admin_id'] ?? (auth()->guard('admin')->id() ?? null);
        $userId = $attributes['user_id'] ?? (auth()->guard('web')->id() ?? auth()->id() ?? null);

        $actorType = $attributes['actor_type'] ?? (
            $adminId ? 'admin' : ($userId ? 'user' : 'system')
        );

        $data = [
            'user_id' => $userId,
            'admin_id' => $adminId,
            'actor_type' => $actorType,
            'action' => $attributes['action'] ?? 'ACTIVITY',
            'description' => $attributes['description'] ?? null,
            'ip_address' => $ip,
            'user_agent' => $userAgent ? substr($userAgent, 0, 500) : null,
            'device' => $attributes['device'] ?? $parsed['device'],
            'browser' => $attributes['browser'] ?? $parsed['browser'],
            'platform' => $attributes['platform'] ?? $parsed['platform'],
            'request_method' => $method ? strtoupper($method) : null,
            'request_url' => $url ? substr($url, 0, 500) : null,
            'properties' => $attributes['properties'] ?? null,
        ];

        // Ensure compatibility with tables where optional columns may not yet be migrated
        $columns = self::getTableColumns();
        if (!empty($columns)) {
            $data = array_intersect_key($data, array_flip($columns));
        }

        try {
            return self::create($data);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('UserActivityLog insert bypassed: ' . $e->getMessage());
            return new self($data);
        }
    }

    /**
     * Cache existing table columns to prevent unknown column SQL exceptions during transitions.
     */
    public static function getTableColumns(): array
    {
        if (!self::tableExists()) {
            return [];
        }

        return Cache::remember('user_activity_logs_columns_cache', 3600, function () {
            try {
                return Schema::getColumnListing('user_activity_logs');
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    /**
     * Parse User-Agent string to extract device, browser, and operating system platform.
     */
    public static function parseUserAgent(?string $userAgent): array
    {
        if (empty($userAgent)) {
            return [
                'device' => 'System / CLI',
                'browser' => 'Automated Engine',
                'platform' => 'Server Environment',
            ];
        }

        $ua = $userAgent;

        // 1. Detect Device Category
        $device = 'Desktop';
        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $ua)) {
            $device = 'Tablet';
        } elseif (preg_match('/(mobile|ipod|iphone|android|blackberry|iemobile|opera mini)/i', $ua)) {
            $device = 'Mobile';
        } elseif (preg_match('/(curl|postman|insomnia|guzzle|python|wget)/i', $ua)) {
            $device = 'API / Bot';
        }

        // 2. Detect Operating System / Platform
        $platform = 'Unknown OS';
        if (preg_match('/iphone/i', $ua)) {
            $platform = 'iOS (iPhone)';
        } elseif (preg_match('/ipad/i', $ua)) {
            $platform = 'iOS (iPad)';
        } elseif (preg_match('/android/i', $ua)) {
            $platform = 'Android';
        } elseif (preg_match('/windows nt 10\.0/i', $ua)) {
            $platform = 'Windows 10/11';
        } elseif (preg_match('/windows nt 6\.3/i', $ua)) {
            $platform = 'Windows 8.1';
        } elseif (preg_match('/windows nt 6\.2/i', $ua)) {
            $platform = 'Windows 8';
        } elseif (preg_match('/windows nt 6\.1/i', $ua)) {
            $platform = 'Windows 7';
        } elseif (preg_match('/macintosh|mac os x/i', $ua)) {
            $platform = 'macOS';
        } elseif (preg_match('/ubuntu/i', $ua)) {
            $platform = 'Ubuntu Linux';
        } elseif (preg_match('/linux/i', $ua)) {
            $platform = 'Linux';
        }

        // 3. Detect Web Browser
        $browser = 'Unknown Browser';
        if (preg_match('/edg\/([0-9\.]+)/i', $ua, $matches)) {
            $browser = 'Microsoft Edge ' . explode('.', $matches[1])[0];
        } elseif (preg_match('/brave\/([0-9\.]+)/i', $ua, $matches)) {
            $browser = 'Brave ' . explode('.', $matches[1])[0];
        } elseif (preg_match('/opr\/([0-9\.]+)/i', $ua, $matches)) {
            $browser = 'Opera ' . explode('.', $matches[1])[0];
        } elseif (preg_match('/chrome\/([0-9\.]+)/i', $ua, $matches)) {
            $browser = 'Google Chrome ' . explode('.', $matches[1])[0];
        } elseif (preg_match('/version\/([0-9\.]+).*safari/i', $ua, $matches)) {
            $browser = 'Safari ' . explode('.', $matches[1])[0];
        } elseif (preg_match('/firefox\/([0-9\.]+)/i', $ua, $matches)) {
            $browser = 'Mozilla Firefox ' . explode('.', $matches[1])[0];
        } elseif (preg_match('/postmanruntime/i', $ua)) {
            $browser = 'Postman Client';
        } elseif (preg_match('/curl\/([0-9\.]+)/i', $ua, $matches)) {
            $browser = 'cURL ' . $matches[1];
        }

        return [
            'device' => $device,
            'browser' => $browser,
            'platform' => $platform,
        ];
    }

    /**
     * Resolve Actor Name (User, Admin, or System).
     */
    public function getActorNameAttribute(): string
    {
        if ($this->actor_type === 'admin' && $this->admin) {
            return $this->admin->name . ' (Staff)';
        }
        if ($this->user) {
            return $this->user->name;
        }
        if ($this->actor_type === 'admin') {
            return 'Administrator';
        }
        return 'System Automation';
    }

    /**
     * Resolve Actor Email.
     */
    public function getActorEmailAttribute(): string
    {
        if ($this->actor_type === 'admin' && $this->admin) {
            return $this->admin->email;
        }
        if ($this->user) {
            return $this->user->email;
        }
        return '-';
    }

    /**
     * Resolve Actor Role Label.
     */
    public function getActorRoleAttribute(): string
    {
        return match ($this->actor_type) {
            'admin' => 'Administrator',
            'user' => 'Customer',
            default => 'System Automation',
        };
    }

    /**
     * Resolve Actor Role Badge Color.
     */
    public function getActorBadgeColorAttribute(): string
    {
        return match ($this->actor_type) {
            'admin' => 'warning',
            'user' => 'info',
            default => 'gray',
        };
    }

    /**
     * Resolve Event Category.
     */
    public function getEventCategoryAttribute(): string
    {
        return match ($this->action) {
            'LOGIN', 'LOGOUT', 'ADMIN_LOGIN', 'ADMIN_LOGOUT', 'FAILED_LOGIN', 'REGISTERED', 'PASSWORD_RESET', 'PASSWORD_CHANGED'
                => 'Authentication',
            'ORDER_PLACED', 'PAYMENT_SUBMITTED', 'INVOICE_PAID', 'CREDIT_ADDED', 'CREDIT_DEPOSIT_INITIATED', 'STAFF_CREDIT_ADDED', 'SERVICE_RENEWED'
                => 'Billing & Finance',
            'SERVER_STARTED', 'SERVER_REBOOTED', 'SERVER_STOPPED', 'SERVER_SHUTDOWN', 'SERVER_PASSWORD_RESET', 'SERVER_RESCUE_MODE'
                => 'Cloud VPS Infrastructure',
            'TICKET_OPENED', 'TICKET_REPLIED', 'STAFF_TICKET_REPLY', 'TICKET_CLOSED', 'TICKET_REOPENED'
                => 'Support Helpdesk',
            'PORTAL_BLOCKED', 'PORTAL_RESTORED', 'PROFILE_UPDATED'
                => 'Account & Security Settings',
            default
                => 'General Operations',
        };
    }

    /**
     * Resolve Severity Color.
     */
    public function getSeverityAttribute(): string
    {
        return match ($this->action) {
            'ORDER_PLACED', 'INVOICE_PAID', 'CREDIT_ADDED', 'PORTAL_RESTORED', 'REGISTERED' => 'success',
            'PROFILE_UPDATED', 'PASSWORD_CHANGED', 'PASSWORD_RESET', 'SERVER_REBOOTED', 'SERVER_PASSWORD_RESET' => 'warning',
            'TICKET_OPENED', 'TICKET_REPLIED', 'STAFF_TICKET_REPLY', 'TICKET_REOPENED' => 'info',
            'CREDIT_DEPOSIT_INITIATED', 'STAFF_CREDIT_ADDED', 'SERVER_STARTED', 'SERVICE_RENEWED' => 'primary',
            'PORTAL_BLOCKED', 'SERVER_STOPPED', 'SERVER_SHUTDOWN', 'TICKET_CLOSED', 'SERVER_RESCUE_MODE', 'FAILED_LOGIN' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Resolve the location string with country flag and network indicators.
     */
    public function getLocationAttribute(): string
    {
        $ip = $this->ip_address;
        $user = $this->user;

        $country = $user?->country ?? '';
        $city = $user?->city ?? '';
        $profileParts = array_filter([$city, $country]);
        $profileLocation = implode(', ', $profileParts);
        $flag = $this->country_flag;

        if (empty($ip) || $ip === '-') {
            return $profileLocation ? "{$flag} {$profileLocation}" : 'Unknown Location';
        }

        // Check if IP is in a private, loopback, or reserved range
        $isPrivate = !filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );

        if ($isPrivate) {
            if ($profileLocation) {
                return "{$flag} {$profileLocation} (LAN)";
            }
            return 'Local Network (LAN / Loopback)';
        }

        // For public IPs, cache lookup for 7 days to avoid repeated network latency
        return Cache::remember("geoip_{$ip}", 86400 * 7, function () use ($ip, $flag, $profileLocation) {
            try {
                $ctx = stream_context_create(['http' => ['timeout' => 2]]);
                $json = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,city,countryCode", false, $ctx);
                if ($json) {
                    $data = json_decode($json, true);
                    if (($data['status'] ?? '') === 'success') {
                        $c = $data['country'] ?? '';
                        $ci = $data['city'] ?? '';
                        $resolved = implode(', ', array_filter([$ci, $c]));
                        $code = strtolower($data['countryCode'] ?? '');
                        $ipFlag = self::resolveCountryFlag($code);
                        if ($resolved) {
                            return "{$ipFlag} {$resolved}";
                        }
                    }
                }
            } catch (\Throwable $e) {}

            return $profileLocation ? "{$flag} {$profileLocation}" : 'Global / Public IP';
        });
    }

    /**
     * Resolve a country name or ISO code to its corresponding vector emoji flag.
     */
    public static function resolveCountryFlag(?string $countryOrCode): string
    {
        if (empty($countryOrCode)) {
            return '🌐';
        }
        $c = strtolower(trim($countryOrCode));

        return match (true) {
            $c === 'us' || str_contains($c, 'united states') => '🇺🇸',
            $c === 'gb' || $c === 'uk' || str_contains($c, 'united kingdom') => '🇬🇧',
            $c === 'de' || str_contains($c, 'germany') => '🇩🇪',
            $c === 'ca' || str_contains($c, 'canada') => '🇨🇦',
            $c === 'fr' || str_contains($c, 'france') => '🇫🇷',
            $c === 'sg' || str_contains($c, 'singapore') => '🇸🇬',
            $c === 'nl' || str_contains($c, 'netherlands') => '🇳🇱',
            $c === 'au' || str_contains($c, 'australia') => '🇦🇺',
            $c === 'in' || str_contains($c, 'india') => '🇮🇳',
            $c === 'bd' || str_contains($c, 'bangladesh') => '🇧🇩',
            $c === 'jp' || str_contains($c, 'japan') => '🇯🇵',
            $c === 'es' || str_contains($c, 'spain') => '🇪🇸',
            $c === 'it' || str_contains($c, 'italy') => '🇮🇹',
            $c === 'br' || str_contains($c, 'brazil') => '🇧🇷',
            default => '🌐',
        };
    }

    public function getCountryFlagAttribute(): string
    {
        return self::resolveCountryFlag($this->user?->country);
    }
}
