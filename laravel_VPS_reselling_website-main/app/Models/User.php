<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

#[Fillable([
    'name',
    'email',
    'phone',
    'company_name',
    'address',
    'city',
    'state',
    'country',
    'zip_code',
    'notification_preferences',
    'password',
    'is_suspended',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'customer' && !$this->is_suspended;
    }
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company_name',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'notification_preferences',
        'password',
        'is_suspended',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_preferences' => 'array',
        ];
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function services(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function invoices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function credits(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invoice::class)->whereNull('order_id')->whereNull('service_id');
    }

    public const ACTIVE_SERVICE_STATUSES = [
        'active',
        'provisioning',
        'awaiting_provisioning',
        'ready_for_provisioning',
        'contabo_ok',
        'provisioned',
    ];

    public const SUSPENDED_SERVICE_STATUSES = [
        'suspended',
    ];

    /**
     * Scope for Active Customers (has >= 1 active/provisioning service)
     */
    public function scopeActiveCustomer(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereHas('services', fn ($q) => $q->whereIn('status', self::ACTIVE_SERVICE_STATUSES));
    }

    /**
     * Scope for Suspended Customers (has suspended service(s) and 0 active)
     */
    public function scopeSuspendedCustomer(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereHas('services', fn ($q) => $q->whereIn('status', self::SUSPENDED_SERVICE_STATUSES))
            ->whereDoesntHave('services', fn ($q) => $q->whereIn('status', self::ACTIVE_SERVICE_STATUSES));
    }

    /**
     * Scope for Inactive Customers (0 active or suspended services)
     */
    public function scopeInactiveCustomer(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereDoesntHave('services', fn ($q) => $q->whereIn('status', array_merge(self::ACTIVE_SERVICE_STATUSES, self::SUSPENDED_SERVICE_STATUSES)));
    }

    /**
     * Dynamic computed customer status based on live database services.
     */
    public function getCustomerStatusAttribute(): string
    {
        $active = isset($this->active_services_count)
            ? (int) $this->active_services_count
            : $this->services()->whereIn('status', self::ACTIVE_SERVICE_STATUSES)->count();

        if ($active > 0) {
            return "Active ({$active})";
        }

        $suspended = isset($this->suspended_services_count)
            ? (int) $this->suspended_services_count
            : $this->services()->whereIn('status', self::SUSPENDED_SERVICE_STATUSES)->count();

        if ($suspended > 0) {
            return "Suspended ({$suspended})";
        }

        return 'Inactive';
    }

    public function supportTickets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function notes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserNote::class);
    }

    public function emailLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserEmailLog::class)->orderByDesc('sent_at');
    }

    public function activityLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserActivityLog::class)->orderByDesc('created_at');
    }

    /**
     * Compute current account credit balance from paid deposit invoices.
     */
    public function getCreditBalanceAttribute(): float
    {
        $deposits = $this->invoices()
            ->whereNull('order_id')
            ->whereNull('service_id')
            ->where('status', 'paid')
            ->sum('total');

        return round((float) $deposits, 2);
    }
}
