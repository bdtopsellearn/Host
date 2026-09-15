<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renewal Invoice Generated</title>
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
            background: linear-gradient(135deg, #4c1d95, #673de6);
            padding: 36px 30px;
            text-align: center;
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
            color: #e9d5ff;
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
        .badge-warning {
            background-color: #d97706;
            color: #ffffff;
            padding: 3px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: bold;
        }
        .policy-box {
            background-color: #1e0b38;
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            padding: 14px 18px;
            margin: 20px 0;
            font-size: 13px;
            color: #cbd5e1;
        }
        .policy-box strong {
            color: #fbbf24;
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
        <div class="header">
            <h1>📄 Renewal Invoice Generated</h1>
            <p>Invoice #{{ $invoice->invoice_number }} for {{ $service->package->name ?? 'Cloud VPS' }}</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hello <strong>{{ $user->name }}</strong>,
            </div>
            <p style="color: #cbd5e1; font-size: 15px; margin-bottom: 20px;">
                Your upcoming VPS renewal invoice has been generated. Please review the details below and settle the balance on or before the due date to ensure continuous, uninterrupted uptime.
            </p>

            <!-- Invoice Overview Card -->
            <div class="card">
                <div class="card-title">Invoice & Service Summary</div>
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
                    <span class="info-label">Billing Cycle:</span>
                    <span class="info-value">{{ ucfirst($service->billing_cycle ?? 'monthly') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Due Date:</span>
                    <span class="info-value" style="color: #f59e0b; font-weight: bold;">
                        {{ \Carbon\Carbon::parse($invoice->due_date ?? $service->next_due_date)->format('M d, Y') }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value"><span class="badge-warning">UNPAID</span></span>
                </div>
                <div class="info-row" style="padding-top: 12px; border-top: 2px solid #2e1065;">
                    <span class="info-label" style="font-size: 16px; font-weight: bold; color: #ffffff;">Total Amount Due:</span>
                    <span class="info-value" style="font-size: 18px; font-weight: bold; color: #34d399;">
                        ${{ number_format((float) ($invoice->total ?? $invoice->amount), 2) }} USD
                    </span>
                </div>
            </div>

            <!-- Zero Grace Period Policy Notice -->
            <div class="policy-box">
                <strong>⚠️ Strict Reseller Zero-Grace-Period Expiration Policy:</strong><br>
                Because upstream bare-metal infrastructure cycles renew strictly upfront, VPS instances that are not renewed by <strong>23:59 UTC on the due date</strong> are subject to immediate cancellation and permanent hypervisor deletion. To protect your server data, please ensure payment is completed before the due date.
            </div>

            <!-- CTA Button -->
            <div class="btn-container">
                <a href="{{ url('/customer/invoices') }}" class="btn">
                    Pay Invoice Now &rarr;
                </a>
            </div>

            <p style="color: #64748b; font-size: 13px; text-align: center; margin-top: 20px;">
                💡 <em>Supported payment methods include Credit/Debit Cards (Stripe) and manual Cryptocurrency (USDT, BTC, ETH, SOL).</em>
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>Questions? Contact our 24/7 support team via your <a href="{{ url('/customer') }}">Customer Portal</a>.</p>
        </div>
    </div>
</body>
</html>
