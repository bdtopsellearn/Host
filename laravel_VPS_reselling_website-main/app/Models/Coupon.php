<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'usage_limit',
        'used_count',
        'expiry_date',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'expiry_date' => 'date',
        ];
    }
}
