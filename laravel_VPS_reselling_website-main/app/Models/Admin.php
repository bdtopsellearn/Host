<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable implements FilamentUser
{
    use HasFactory, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
