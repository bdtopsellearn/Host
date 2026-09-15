<?php

namespace App\Filament\Customer\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class Login extends BaseLogin
{
    public function getHeading(): string
    {
        return 'Log in';
    }

    public function getSubheading(): \Illuminate\Contracts\Support\Htmlable | string | null
    {
        if (! filament()->hasRegistration()) {
            return 'Access your customer portal, active VPS instances, and invoices.';
        }

        return new \Illuminate\Support\HtmlString(
            '<span class="text-slate-500">Don\'t have an account?</span> ' . $this->registerAction->toHtml()
        );
    }

    public function quickDemoLogin()
    {
        if (!config('app.demo_login_enabled') || app()->environment('production')) {
            return;
        }

        $user = User::where('email', 'test@example.com')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
            ]);
        }

        Auth::guard('web')->login($user, true);

        return redirect()->intended(Filament::getUrl());
    }
}
