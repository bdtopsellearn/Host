<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
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
            background: linear-gradient(135deg, #059669, #0d9488);
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
            color: #a7f3d0;
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
            color: #34d399;
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
        .badge-paid {
            background-color: #059669;
            color: #ffffff;
            padding: 3px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: bold;
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
            <h1>✅ Payment Received</h1>
            <p>Official Receipt for Invoice #{{ $invoice->invoice_number }}</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hello <strong>{{ $user->name }}</strong>,
            </div>
            <p style="color: #cbd5e1; font-size: 15px; margin-bottom: 20px;">
                Thank you for your business. We have successfully processed your payment. Your receipt and transaction details are recorded below:
            </p>

            <!-- Receipt Breakdown Card -->
            <div class="card">
                <div class="card-title">Payment Receipt Summary</div>
                <div class="info-row">
                    <span class="info-label">Invoice Number:</span>
                    <span class="info-value" style="color: #34d399;">#{{ $invoice->invoice_number }}</span>
                </div>
                @if($invoice->service && $invoice->service->package)
                <div class="info-row">
                    <span class="info-label">Service Plan:</span>
                    <span class="info-value">{{ $invoice->service->package->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Server IP:</span>
                    <span class="info-value">{{ $invoice->service->ip_address ?? 'Assigned' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Next Due Date:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($invoice->service->next_due_date)->format('M d, Y') }}</span>
                </div>
                @elseif($invoice->order)
                <div class="info-row">
                    <span class="info-label">Order Number:</span>
                    <span class="info-value">{{ $invoice->order->order_number }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Payment Method:</span>
                    <span class="info-value">{{ ucfirst($invoice->payment_method ?? 'Credit Card / Crypto') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value"><span class="badge-paid">PAID</span></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Paid Timestamp:</span>
                    <span class="info-value">{{ ($invoice->paid_at ? \Carbon\Carbon::parse($invoice->paid_at) : now())->format('M d, Y H:i T') }}</span>
                </div>
                <div class="info-row" style="padding-top: 12px; border-top: 2px solid #2e1065;">
                    <span class="info-label" style="font-size: 16px; font-weight: bold; color: #ffffff;">Amount Paid:</span>
                    <span class="info-value" style="font-size: 18px; font-weight: bold; color: #34d399;">
                        ${{ number_format((float) ($invoice->total ?? $invoice->amount), 2) }} USD
                    </span>
                </div>
            </div>

            <!-- CTA Button -->
            <div class="btn-container">
                <a href="{{ url('/customer/invoices') }}" class="btn">
                    View Invoices in Customer Portal &rarr;
                </a>
            </div>

            <p style="color: #64748b; font-size: 13px; text-align: center; margin-top: 20px;">
                💡 <em>You can view and download all past transaction invoices from your billing dashboard anytime.</em>
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>Need support? Open a ticket in your <a href="{{ url('/customer/support-tickets') }}">Customer Helpdesk</a>.</p>
        </div>
    </div>
</body>
</html>
