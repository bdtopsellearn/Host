<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'specs',
        'price_monthly',
        'price_quarterly',
        'price_semi_annually',
        'price_annually',
        'setup_fee',
        'contabo_product_id',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'specs' => 'array',
            'is_active' => 'boolean',
            'price_monthly' => 'decimal:2',
            'price_quarterly' => 'decimal:2',
            'price_semi_annually' => 'decimal:2',
            'price_annually' => 'decimal:2',
            'setup_fee' => 'decimal:2',
        ];
    }

    public function addons(): HasMany
    {
        return $this->hasMany(PackageAddon::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Retrieve active packages cached for 24 hours.
     */
    public static function getCachedActivePackages(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $cached = \Illuminate\Support\Facades\Cache::remember('catalog:packages:active', 86400, function () {
                return static::where('is_active', true)->orderBy('price_monthly')->get();
            });

            if ($cached instanceof \Illuminate\Database\Eloquent\Collection && $cached->isNotEmpty() && $cached->first() instanceof static) {
                return $cached;
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Cache retrieval failed for packages: ' . $e->getMessage());
        }

        try {
            return static::where('is_active', true)->orderBy('price_monthly')->get();
        } catch (\Throwable $e) {
            return new \Illuminate\Database\Eloquent\Collection();
        }
    }

    /**
     * The "booted" method of the model to handle automatic cache invalidation.
     */
    protected static function booted(): void
    {
        static::saved(function ($package) {
            static::clearCatalogCache($package->id);
        });

        static::deleted(function ($package) {
            static::clearCatalogCache($package->id);
        });
    }

    /**
     * Purge all related catalog cache entries.
     */
    public static function clearCatalogCache(?int $packageId = null): void
    {
        \Illuminate\Support\Facades\Cache::forget('catalog:packages:active');
        \Illuminate\Support\Facades\Cache::forget('catalog:sitemap_xml');
        if ($packageId) {
            \Illuminate\Support\Facades\Cache::forget("catalog:addons:package_{$packageId}");
        }
    }
}

