<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardStats;
use App\Filament\Widgets\DatacenterDistributionChart;
use App\Filament\Widgets\LiveActivityForensicsWidget;
use App\Filament\Widgets\RecentOrdersWidget;
use App\Filament\Widgets\RevenueAnalyticsChart;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Dashboard extends BaseDashboard
{
    public function getTitle(): string | Htmlable
    {
        return 'Dashboard';
    }

    public function getHeading(): string | Htmlable
    {
        $admin = auth('admin')->user();
        $name = $admin?->name ?? 'Administrator';
        $hour = (int) now()->format('H');
        $greeting = match (true) {
            $hour >= 5 && $hour < 12 => 'Good morning',
            $hour >= 12 && $hour < 18 => 'Good afternoon',
            default => 'Good evening',
        };

        return "{$greeting}, {$name}";
    }

    public function getSubheading(): string | Htmlable | null
    {
        $dateTime = now()->format('l, F j, Y · H:i T');

        return new HtmlString(
            '<div style="display: flex; flex-direction: column; gap: 4px; margin-top: 2px;">' .
                '<div style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.75rem; font-weight: 600; color: #673DE6; background: rgba(103, 61, 230, 0.08); border: 1px solid rgba(103, 61, 230, 0.2); padding: 2px 10px; border-radius: 9999px; width: fit-content;">' .
                    '<svg style="width: 12px; height: 12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>' .
                    '<span>' . e($dateTime) . '</span>' .
                '</div>' .
                '<span style="font-size: 0.875rem; color: #64748B; margin-top: 2px;">' .
                    'All core infrastructure operational. Here is an overview of platform revenue, cloud nodes, and deployment queue.' .
                '</span>' .
            '</div>'
        );
    }

    public function getColumns(): int | array
    {
        return [
            'default' => 1,
            'sm' => 1,
            'md' => 2,
            'lg' => 2,
            'xl' => 2,
        ];
    }

    public function getWidgets(): array
    {
        return [
            DashboardStats::class,
            RevenueAnalyticsChart::class,
            DatacenterDistributionChart::class,
            RecentOrdersWidget::class,
            LiveActivityForensicsWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createPackage')
                ->label('Add VPS Package')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(url('/admin/packages/create')),
        ];
    }
}

