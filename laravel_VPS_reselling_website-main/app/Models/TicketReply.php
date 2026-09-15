<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'support_ticket_id',
        'user_id',
        'admin_id',
        'message',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function getIsStaffReplyAttribute(): bool
    {
        return !empty($this->admin_id);
    }

    public function getAuthorNameAttribute(): string
    {
        if ($this->admin) {
            return $this->admin->name . ' (VortexCloud Support)';
        }

        return $this->user?->name ?? 'Customer';
    }
}
