<?php

namespace App\Filament\Resources\ActivityLogs\Widgets;

use App\Models\UserActivityLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ActivityLogStats extends BaseWidget
{
    protected function getStats(): array
    {
        if (!UserActivityLog::tableExists()) {
            return [
                Stat::make('Total Audit Logs', '0')
                    ->description('Migration required (php artisan migrate)')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('gray'),

                Stat::make('Active Customers (24h)', '0')
                    ->description('Database table pending')
                    ->color('gray'),

                Stat::make('Security & Auth (Today)', '0')
                    ->description('Database table pending')
                    ->color('gray'),

                Stat::make('Commerce & Billing (Today)', '0')
                    ->description('Database table pending')
                    ->color('gray'),
            ];
        }

        $totalLogs = UserActivityLog::count();
        $todayLogs = UserActivityLog::whereDate('created_at', today())->count();

        $activeUsers24h = UserActivityLog::where('created_at', '>=', now()->subHours(24))
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');

        $securityEvents = UserActivityLog::whereIn('action', [
            'LOGIN', 'ADMIN_LOGIN', 'FAILED_LOGIN', 'PASSWORD_RESET', 'PASSWORD_CHANGED',
            'PORTAL_BLOCKED', 'PORTAL_RESTORED', 'SERVER_STOPPED', 'SERVER_SHUTDOWN', 'SERVER_RESCUE_MODE'
        ])->whereDate('created_at', today())->count();

        $billingEvents = UserActivityLog::whereIn('action', [
            'ORDER_PLACED', 'PAYMENT_SUBMITTED', 'INVOICE_PAID', 'CREDIT_ADDED', 'CREDIT_DEPOSIT_INITIATED', 'STAFF_CREDIT_ADDED', 'SERVICE_RENEWED'
        ])->whereDate('created_at', today())->count();

        return [
            Stat::make('Total Audit Logs', number_format($totalLogs))
                ->description("{$todayLogs} events captured today")
                ->descriptionIcon('heroicon-m-finger-print')
                ->color('primary'),

            Stat::make('Active Customers (24h)', number_format($activeUsers24h))
                ->description('Unique accounts interacting')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Security & Auth (Today)', number_format($securityEvents))
                ->description('Logins, credentials & server power')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color($securityEvents > 0 ? 'warning' : 'gray'),

            Stat::make('Commerce & Billing (Today)', number_format($billingEvents))
                ->description('Orders, deposits & renewals')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
