<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'package_id',
        'contabo_instance_id',
        'server_name',
        'default_user',
        'os_image',
        'region',
        'cpu_cores',
        'ram_size',
        'disk_size',
        'ip_address',
        'encrypted_credentials',
        'status',
        'billing_cycle',
        'recurring_amount',
        'specs_snapshot',
        'active_addons',
        'next_due_date',
    ];

    protected function casts(): array
    {
        return [
            'next_due_date' => 'date',
            'recurring_amount' => 'decimal:2',
            'specs_snapshot' => 'array',
            'active_addons' => 'array',
        ];
    }

    /**
     * Decrypt and return the root password on demand.
     */
    public function getDecryptedPasswordAttribute(): ?string
    {
        if (empty($this->encrypted_credentials)) {
            return null;
        }

        // 1. Check if encrypted_credentials is a JSON string of configuration
        $data = is_array($this->encrypted_credentials) 
            ? $this->encrypted_credentials 
            : json_decode($this->encrypted_credentials, true);

        if (is_array($data)) {
            if (!empty($data['root_password'])) {
                try {
                    return \Illuminate\Support\Facades\Crypt::decryptString($data['root_password']);
                } catch (\Throwable $e) {
                    try {
                        return decrypt($data['root_password']);
                    } catch (\Throwable $e2) {
                        return $data['root_password'];
                    }
                }
            }
            return null;
        }

        // 2. Standard Laravel decrypt
        try {
            return \Illuminate\Support\Facades\Crypt::decryptString($this->encrypted_credentials);
        } catch (\Throwable $e) {
            try {
                return decrypt($this->encrypted_credentials);
            } catch (\Throwable $e2) {
                return null;
            }
        }
    }

    /**
     * Get the customer-configured or assigned server hostname.
     */
    public function getFormattedHostnameAttribute(): string
    {
        if (!empty($this->server_name)) {
            return $this->server_name;
        }

        if (!empty($this->encrypted_credentials)) {
            $data = is_array($this->encrypted_credentials) 
                ? $this->encrypted_credentials 
                : json_decode($this->encrypted_credentials, true);

            if (!empty($data['hostname'])) {
                return $data['hostname'];
            }
        }

        return 'vps-' . $this->id . '.vortexcloud.net';
    }

    /**
     * Get the human-readable datacenter location.
     */
    public function getFormattedRegionAttribute(): string
    {
        if (!empty($this->specs_snapshot['datacenter'])) {
            return $this->specs_snapshot['datacenter'];
        }

        if (!empty($this->region)) {
            return strtoupper($this->region);
        }

        return 'Global';
    }

    /**
     * Get a compact specs summary (e.g. 8 vCPU Cores • 24 GB RAM • 100 GB NVMe).
     */
    public function getSpecsSummaryAttribute(): string
    {
        $specs = $this->specs_snapshot ?? [];
        $parts = [];
        if (!empty($specs['cores'])) $parts[] = $specs['cores'];
        if (!empty($specs['memory'])) $parts[] = $specs['memory'];
        if (!empty($specs['storage'])) $parts[] = $specs['storage'];

        return !empty($parts) ? implode(' • ', $parts) : '';
    }

    /**
     * Get recurring price formatted like $322.56 USD.
     */
    public function getFormattedPricingAttribute(): string
    {
        $amount = number_format((float) ($this->recurring_amount ?? $this->package?->price ?? 0), 2);
        return '$' . $amount . ' USD';
    }

    /**
     * Get clean billing cycle name.
     */
    public function getFormattedBillingCycleAttribute(): string
    {
        return match (strtolower($this->billing_cycle ?? '')) {
            'biennially', '24months' => 'Biennially',
            'annually', '12months' => 'Annually',
            'monthly', '1month' => 'Monthly',
            default => !empty($this->billing_cycle) ? ucfirst($this->billing_cycle) : 'Monthly',
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function provisioningLogs(): HasMany
    {
        return $this->hasMany(ProvisioningLog::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Extend service next_due_date according to billing_cycle and set status back to active.
     */
    public function extendBillingCycle(): void
    {
        $currentDue = $this->next_due_date ? \Carbon\Carbon::parse($this->next_due_date) : now();
        $baseDate = $currentDue->isPast() ? now() : $currentDue;

        $newDueDate = match (strtolower($this->billing_cycle ?? 'monthly')) {
            'biennially', '24months' => $baseDate->copy()->addMonths(24),
            'annually', '12months' => $baseDate->copy()->addMonths(12),
            default => $baseDate->copy()->addMonth(),
        };

        $this->update([
            'next_due_date' => $newDueDate,
            'status' => 'active',
        ]);
    }
}
