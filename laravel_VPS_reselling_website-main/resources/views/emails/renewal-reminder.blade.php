<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renewal Reminder</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #0c0217;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #e2e8f0;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #16072b;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #2e1065;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.6);
        }
        .header {
            padding: 36px 30px;
            text-align: center;
        }
        .header-normal {
            background: linear-gradient(135deg, #4c1d95, #673de6);
        }
        .header-urgent {
            background: linear-gradient(135deg, #b45309, #d97706);
        }
        .header-final {
            background: linear-gradient(135deg, #991b1b, #dc2626);
        }
        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 8px 0 0 0;
            color: #fef08a;
            font-size: 15px;
        }
        .content {
            padding: 32px 30px;
        }
        .greeting {
            font-size: 17px;
            color: #f8fafc;
            margin-bottom: 20px;
        }
        .card {
            background-color: #0f0521;
            border: 1px solid #2e1065;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .card-title {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #a855f7;
            font-weight: 700;
            margin-bottom: 14px;
            border-bottom: 1px solid #2e1065;
            padding-bottom: 8px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #1e0b38;
            font-size: 14px;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #94a3b8;
            font-weight: 500;
        }
        .info-value {
            color: #f1f5f9;
            font-weight: 600;
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
        }
        .badge-danger {
            background-color: #dc2626;
            color: #ffffff;
            padding: 3px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-warning {
            background-color: #d97706;
            color: #ffffff;
            padding: 3px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: bold;
        }
        .policy-box-urgent {
            background-color: #2a0b12;
            border-left: 4px solid #ef4444;
            border-radius: 8px;
            padding: 16px 20px;
            margin: 20px 0;
            font-size: 13px;
            color: #fca5a5;
        }
        .policy-box-urgent strong {
            color: #fecaca;
            font-size: 14px;
        }
        .btn-container {
            text-align: center;
            margin: 32px 0 16px 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #673de6, #7c3aed);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 36px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 4px 14px rgba(103, 61, 230, 0.45);
        }
        .btn-danger {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.45);
        }
        .footer {
            background-color: #0c0217;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #2e1065;
        }
        .footer a {
            color: #a855f7;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header {{ $daysRemaining === 0 ? 'header-final' : ($daysRemaining <= 3 ? 'header-urgent' : 'header-normal') }}">
            @if($daysRemaining === 0)
                <h1>🚨 FINAL WARNING: Due Today</h1>
                <p>Immediate Termination at 23:59 UTC if unpaid</p>
            @elseif($daysRemaining <= 3)
                <h1>⚠️ Urgent: Renewal Due in {{ $daysRemaining }} Days</h1>
                <p>Invoice #{{ $invoice->invoice_number }} Requires Immediate Action</p>
            @else
                <h1>⏰ Friendly Reminder: Renewal in {{ $daysRemaining }} Days</h1>
                <p>Invoice #{{ $invoice->invoice_number }} for {{ $service->package->name ?? 'Cloud VPS' }}</p>
            @endif
        </div>

        <div class="content">
            <div class="greeting">
                Hello <strong>{{ $user->name }}</strong>,
            </div>

            @if($daysRemaining === 0)
                <p style="color: #fca5a5; font-size: 15px; font-weight: 600; margin-bottom: 20px;">
                    This is your <strong>FINAL NOTICE</strong>. Your VPS service renewal is due <strong>TODAY</strong>. Because we operate as an upstream cloud reseller with strict monthly supplier commitments, any service remaining unpaid by 23:59 UTC tonight will be automatically cancelled and irreversibly terminated.
                </p>
            @elseif($daysRemaining <= 3)
                <p style="color: #fed7aa; font-size: 15px; margin-bottom: 20px;">
                    This is an urgent reminder that your VPS renewal is due in <strong>{{ $daysRemaining }} days</strong>. Please settle Invoice #{{ $invoice->invoice_number }} promptly to prevent service suspension and permanent data decommissioning.
                </p>
            @else
                <p style="color: #cbd5e1; font-size: 15px; margin-bottom: 20px;">
                    This is a reminder that Invoice #{{ $invoice->invoice_number }} for your VPS service is due in <strong>{{ $daysRemaining }} days</strong>.
                </p>
            @endif

            <!-- Invoice Overview Card -->
            <div class="card">
                <div class="card-title">Renewal Details</div>
                <div class="info-row">
                    <span class="info-label">Invoice Number:</span>
                    <span class="info-value" style="color: #a855f7;">#{{ $invoice->invoice_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Service Plan:</span>
                    <span class="info-value">{{ $service->package->name ?? 'Cloud VPS' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">IP Address:</span>
                    <span class="info-value">{{ $service->ip_address ?? 'Assigned' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Due Date:</span>
                    <span class="info-value" style="color: #ef4444; font-weight: bold;">
                        {{ \Carbon\Carbon::parse($invoice->due_date ?? $service->next_due_date)->format('M d, Y') }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Time Remaining:</span>
                    <span class="info-value">
                        @if($daysRemaining === 0)
                            <span class="badge-danger">TODAY (HOURS REMAINING)</span>
                        @else
                            <span class="badge-warning">{{ $daysRemaining }} DAYS</span>
                        @endif
                    </span>
                </div>
                <div class="info-row" style="padding-top: 12px; border-top: 2px solid #2e1065;">
                    <span class="info-label" style="font-size: 16px; font-weight: bold; color: #ffffff;">Amount Due:</span>
                    <span class="info-value" style="font-size: 18px; font-weight: bold; color: #34d399;">
                        ${{ number_format((float) ($invoice->total ?? $invoice->amount), 2) }} USD
                    </span>
                </div>
            </div>

            <!-- Zero Grace Period Warning Notice -->
            <div class="policy-box-urgent">
                <strong>⚠️ Zero-Grace-Period Upstream Reseller Policy:</strong><br>
                Under Section 6 of our Terms of Service, we do not provide grace periods past the due date. Unrenewed virtual instances are decommissioned from upstream bare-metal nodes and all SSD/NVMe partitions are zeroed out immediately upon expiration. We cannot recover data once termination occurs.
            </div>

            <!-- CTA Button -->
            <div class="btn-container">
                <a href="{{ url('/customer/invoices') }}" class="btn {{ $daysRemaining === 0 ? 'btn-danger' : '' }}">
                    Pay ${{ number_format((float) ($invoice->total ?? $invoice->amount), 2) }} USD Now &rarr;
                </a>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>Questions? Contact our 24/7 support team via your <a href="{{ url('/customer') }}">Customer Portal</a>.</p>
        </div>
    </div>
</body>
</html>
