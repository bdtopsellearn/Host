<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Customer\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class CustomerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('customer')
            ->path('customer')
            ->login(\App\Filament\Customer\Pages\Auth\Login::class)
            ->registration(\App\Filament\Customer\Pages\Auth\Register::class)
            ->passwordReset()
            ->profile(\App\Filament\Customer\Pages\Auth\EditProfile::class, isSimple: false)
            ->authGuard('web')
            ->brandName('VortexCloud Customer Portal')
            ->darkMode(false)
            ->colors([
                'primary' => '#673DE6',
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_START,
                fn () => view('filament.customer.components.auth-decorations')
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn () => view('filament.customer.components.demo-login')
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn () => view('filament.components.sub-menu-tree-styles')
            )
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('15.5rem')
            ->collapsedSidebarWidth('4.5rem')
            ->maxContentWidth(\Filament\Support\Enums\Width::Full)
            ->userMenuItems([
                \Filament\Navigation\MenuItem::make()
                    ->label('Back to Website')
                    ->url(fn (): string => url('/'))
                    ->icon('heroicon-o-globe-alt'),
            ])
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make('Settings')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible(true),
            ])
            ->navigationItems([
                \Filament\Navigation\NavigationItem::make('Back to Website')
                    ->url(fn (): string => url('/'))
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->sort(-10),
                \Filament\Navigation\NavigationItem::make('Profile Setting')
                    ->group('Settings')
                    ->sort(1)
                    ->url(fn (): string => url('/customer/profile'))
                    ->isActiveWhen(fn (): bool => request()->is('customer/profile*')),
            ])
            ->discoverResources(in: app_path('Filament/Customer/Resources'), for: 'App\Filament\Customer\Resources')
            ->discoverPages(in: app_path('Filament/Customer/Pages'), for: 'App\Filament\Customer\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Customer/Widgets'), for: 'App\Filament\Customer\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
