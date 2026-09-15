<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Service;
use App\Models\SupportTicket;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class DashboardStats extends Widget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'sm' => 'full',
        'md' => 'full',
        'lg' => 'full',
        'xl' => 'full',
    ];

    protected ?string $pollingInterval = '30s';

    protected string $view = 'filament.widgets.dashboard-stats';

    public function getViewData(): array
    {
        // 1. Total Paid Revenue & 7-Day Sparkline
        $totalRevenue = (float) Invoice::where('status', 'paid')->sum('total');
        
        $revenueSparkline = [];
        $maxDailyRev = 1;
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dailyRev = (float) Invoice::where('status', 'paid')
                ->whereDate('paid_at', $date)
                ->sum('total');
            $val = (int) round($dailyRev);
            $revenueSparkline[] = $val;
            if ($val > $maxDailyRev) {
                $maxDailyRev = $val;
            }
        }

        // 2. Active Cloud VPS Nodes
        $activeServicesCount = Service::where('status', 'active')->count();
        $totalServicesCount = Service::count();

        // 3. Pending Deployment Queue
        $pendingOrdersCount = Order::whereIn('status', ['pending', 'payment_confirmed', 'pending_approval'])->count();
        $unpaidInvoicesCount = Invoice::whereIn('status', ['pending', 'unpaid'])->count();

        // 4. Client Base & Support Workload
        $totalUsers = User::count();
        $openTicketsCount = SupportTicket::whereIn('status', ['open', 'in_progress'])->count();

        return [
            'totalRevenue' => $totalRevenue,
            'revenueSparkline' => $revenueSparkline,
            'maxDailyRev' => $maxDailyRev,
            'activeServicesCount' => $activeServicesCount,
            'totalServicesCount' => $totalServicesCount,
            'pendingOrdersCount' => $pendingOrdersCount,
            'unpaidInvoicesCount' => $unpaidInvoicesCount,
            'totalUsers' => $totalUsers,
            'openTicketsCount' => $openTicketsCount,
        ];
    }
}
