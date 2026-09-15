<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class RevenueAnalyticsChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
    ];

    protected ?string $heading = 'Revenue & Orders Velocity Analytics';

    protected ?string $description = 'Settled cryptocurrency receipts and new customer server provisioning demand.';

    protected ?string $maxHeight = '260px';

    protected ?string $pollingInterval = '60s';

    public ?string $filter = '30';

    protected function getFilters(): ?array
    {
        return [
            '7' => 'Last 7 Days',
            '30' => 'Last 30 Days',
            '90' => 'Last 90 Days',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $days = (int) ($this->filter ?? '30');
        if (!in_array($days, [7, 30, 90])) {
            $days = 30;
        }

        $labels = [];
        $revenueData = [];
        $ordersData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format($days > 30 ? 'M d' : ($days > 7 ? 'M d' : 'D, M d'));

            $dailyRevenue = (float) Invoice::where('status', 'paid')
                ->whereDate('paid_at', $date)
                ->sum('total');
            $revenueData[] = round($dailyRevenue, 2);

            $dailyOrders = Order::whereDate('created_at', $date)->count();
            $ordersData[] = $dailyOrders;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Settled Revenue ($)',
                    'data' => $revenueData,
                    'borderColor' => '#673DE6',
                    'backgroundColor' => 'rgba(103, 61, 230, 0.14)',
                    'fill' => 'start',
                    'tension' => 0.35,
                    'pointBackgroundColor' => '#673DE6',
                    'pointRadius' => $days <= 14 ? 4 : 2,
                    'pointHoverRadius' => 6,
                ],
                [
                    'label' => 'Orders Placed',
                    'data' => $ordersData,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.08)',
                    'fill' => false,
                    'tension' => 0.35,
                    'pointBackgroundColor' => '#10B981',
                    'pointRadius' => $days <= 14 ? 3 : 1,
                    'pointHoverRadius' => 5,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
