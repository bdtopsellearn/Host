<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->authGuard('admin')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->brandName('VortexCloud Admin')
            ->colors([
                'primary' => '#673DE6',
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn () => view('filament.components.admin-demo-login')
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn () => view('filament.components.sub-menu-tree-styles')
            )
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('15.5rem')
            ->collapsedSidebarWidth('4.5rem')
            ->maxContentWidth(\Filament\Support\Enums\Width::Full)
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make('Orders')
                    ->icon('heroicon-o-shopping-bag')
                    ->collapsible(true),
                \Filament\Navigation\NavigationGroup::make('Invoices')
                    ->icon('heroicon-o-document-currency-dollar')
                    ->collapsible(true),
                \Filament\Navigation\NavigationGroup::make('Packages')
                    ->icon('heroicon-o-server-stack')
                    ->collapsible(true),
                \Filament\Navigation\NavigationGroup::make('Support Tickets')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->collapsible(true),
                \Filament\Navigation\NavigationGroup::make('Users')
                    ->icon('heroicon-o-users')
                    ->collapsible(true),
                \Filament\Navigation\NavigationGroup::make('System Settings')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible(true),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
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
