<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Cancelled & Terminated</title>
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
            border: 1px solid #450a0a;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.6);
        }
        .header {
            background: linear-gradient(135deg, #7f1d1d, #991b1b);
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
            color: #fecaca;
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
            border: 1px solid #450a0a;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .card-title {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #f87171;
            font-weight: 700;
            margin-bottom: 14px;
            border-bottom: 1px solid #450a0a;
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
        .badge-terminated {
            background-color: #991b1b;
            color: #ffffff;
            padding: 3px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: bold;
        }
        .policy-box {
            background-color: #26050b;
            border-left: 4px solid #ef4444;
            border-radius: 8px;
            padding: 16px 20px;
            margin: 20px 0;
            font-size: 13px;
            color: #cbd5e1;
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
            <h1>🛑 Server Cancelled & Terminated</h1>
            <p>Notice of Decommissioning Due to Non-Renewal</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hello <strong>{{ $user->name }}</strong>,
            </div>
            <p style="color: #cbd5e1; font-size: 15px; margin-bottom: 20px;">
                We are writing to inform you that your virtual private server has been <strong>cancelled and terminated</strong> because the renewal invoice was not settled prior to the expiration deadline.
            </p>

            <!-- Server Summary Card -->
            <div class="card">
                <div class="card-title">Terminated Instance Details</div>
                <div class="info-row">
                    <span class="info-label">Service Plan:</span>
                    <span class="info-value">{{ $service->package->name ?? 'Cloud VPS' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Server IP:</span>
                    <span class="info-value">{{ $service->ip_address ?? 'Released' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value"><span class="badge-terminated">TERMINATED</span></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Termination Timestamp:</span>
                    <span class="info-value" style="color: #f87171;">{{ now()->format('M d, Y H:i T') }}</span>
                </div>
            </div>

            <!-- Zero Grace Period Policy Notice -->
            <div class="policy-box">
                <strong style="color: #fca5a5;">Why was this server terminated?</strong><br>
                Under Section 6 of our Terms of Service, we operate as a direct cloud reseller and cannot hold unpaid upstream hardware nodes beyond the billing due date. The instance has been cancelled with upstream datacenter providers, its IP address returned to the pool, and its NVMe/SSD partitions have been permanently wiped.
            </div>

            <p style="color: #94a3b8; font-size: 14px; margin-top: 20px;">
                If you still need cloud compute resources, you may deploy a fresh virtual instance at any time directly through your portal.
            </p>

            <!-- CTA Button -->
            <div class="btn-container">
                <a href="{{ url('/plans') }}" class="btn">
                    Deploy New Server &rarr;
                </a>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>Need assistance? Contact our team via your <a href="{{ url('/customer') }}">Customer Portal</a>.</p>
        </div>
    </div>
</body>
</html>
