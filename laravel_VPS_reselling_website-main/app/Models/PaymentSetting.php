<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PaymentSetting extends Model
{
    protected $fillable = [
        'gateway_key',
        'gateway_name',
        'account_number',
        'account_type',
        'qr_code_url',
        'instructions',
        'is_active',
        'conversion_rate_bdt_usd',
        'support_phone',
        'support_whatsapp',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'conversion_rate_bdt_usd' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('active_payment_settings');
        });
        static::deleted(function () {
            Cache::forget('active_payment_settings');
        });
    }

    public static function getActiveGateways()
    {
        return Cache::remember('active_payment_settings', 3600, function () {
            return self::where('is_active', true)->get()->keyBy('gateway_key');
        });
    }

    public static function getSetting(string $key): ?self
    {
        return self::where('gateway_key', $key)->first();
    }
}
