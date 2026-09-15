<?php

namespace App\Filament\Customer\Widgets;

use App\Models\Invoice;
use App\Models\Service;
use App\Models\SupportTicket;
use Filament\Widgets\Widget;

class CustomerStatsOverview extends Widget
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

    /**
     * @var view-string
     */
    protected string $view = 'filament.customer.widgets.customer-stats-overview';

    public function getViewData(): array
    {
        $user = auth()->user();
        $userId = $user?->id;

        // 1. Primary Hero Metric: Active Cloud VPS Fleet
        $activeServicesCount = $userId
            ? Service::where('user_id', $userId)->where('status', 'active')->count()
            : 0;
        $totalServicesCount = $userId
            ? Service::where('user_id', $userId)->count()
            : 0;

        // 2. Secondary Metric 1: Account Credit & Wallet Balance
        $creditBalance = $user ? (float) $user->credit_balance : 0.00;

        // 3. Secondary Metric 2: Pending Invoices Awaiting Settlement
        $unpaidInvoicesQuery = $userId
            ? Invoice::where('user_id', $userId)->whereIn('status', ['pending', 'unpaid'])
            : null;
        $unpaidCount = $unpaidInvoicesQuery ? $unpaidInvoicesQuery->count() : 0;
        $unpaidSum = $unpaidInvoicesQuery ? (float) $unpaidInvoicesQuery->sum('total') : 0.00;

        // 4. Secondary Metric 3: Active Support Inquiries & SLA
        $activeTicketsCount = $userId
            ? SupportTicket::where('user_id', $userId)
                ->whereIn('status', ['open', 'answered', 'customer_reply', 'in_progress'])
                ->count()
            : 0;

        return [
            'activeServicesCount' => $activeServicesCount,
            'totalServicesCount' => $totalServicesCount,
            'creditBalance' => $creditBalance,
            'unpaidCount' => $unpaidCount,
            'unpaidSum' => $unpaidSum,
            'activeTicketsCount' => $activeTicketsCount,
        ];
    }
}
