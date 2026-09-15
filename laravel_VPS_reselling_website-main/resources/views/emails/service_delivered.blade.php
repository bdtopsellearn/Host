<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Server is Ready</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #0f172a;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #e2e8f0;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #1e293b;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #334155;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
        }
        .header {
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            padding: 36px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 8px 0 0 0;
            color: #bfdbfe;
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
            background-color: #0f172a;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .card-title {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #38bdf8;
            font-weight: 700;
            margin-bottom: 14px;
            border-bottom: 1px solid #334155;
            padding-bottom: 8px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #1e293b;
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
        .badge {
            background-color: #059669;
            color: #ffffff;
            padding: 3px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: bold;
        }
        .code-box {
            background-color: #020617;
            border: 1px solid #1e293b;
            border-radius: 8px;
            padding: 12px 16px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            color: #38bdf8;
            margin: 10px 0;
            word-break: break-all;
        }
        .btn-container {
            text-align: center;
            margin: 32px 0 16px 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.4);
        }
        .footer {
            background-color: #0f172a;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #334155;
        }
        .footer a {
            color: #38bdf8;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Your Server is Active!</h1>
            <p>Your {{ $service->package->name ?? 'VPS' }} order has been provisioned and delivered.</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hello <strong>{{ $user->name }}</strong>,
            </div>
            <p style="color: #94a3b8; font-size: 15px; margin-bottom: 24px;">
                Thank you for choosing our cloud services. Your virtual private server has been provisioned and is now fully active. Below are your login credentials and connection instructions:
            </p>

            <!-- Server Details Card -->
            <div class="card">
                <div class="card-title">Server Overview</div>
                <div class="info-row">
                    <span class="info-label">Package Plan:</span>
                    <span class="info-value">{{ $service->package->name ?? 'Cloud VPS' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value"><span class="badge">ACTIVE</span></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Region / Datacenter:</span>
                    <span class="info-value">{{ $service->region ?? 'EU' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Operating System:</span>
                    <span class="info-value">{{ $service->os_image ?? 'Ubuntu 22.04 LTS' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Billing Cycle:</span>
                    <span class="info-value">{{ ucfirst($service->billing_cycle ?? 'monthly') }}</span>
                </div>
                @if($service->next_due_date)
                <div class="info-row">
                    <span class="info-label">Next Renewal Date:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($service->next_due_date)->format('M d, Y') }}</span>
                </div>
                @endif
            </div>

            <!-- Login Credentials Card -->
            <div class="card" style="border-color: #3b82f6;">
                <div class="card-title" style="color: #60a5fa;">🔑 Access & Login Credentials</div>
                <div class="info-row">
                    <span class="info-label">Primary IP Address:</span>
                    <span class="info-value" style="color: #38bdf8; font-size: 15px;">{{ $service->ip_address }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Username:</span>
                    <span class="info-value">{{ $username }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Root Password:</span>
                    <span class="info-value" style="color: #f59e0b; font-size: 15px;">{{ $password }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Port:</span>
                    <span class="info-value">{{ strtolower($service->package->category ?? '') === 'rdp' ? '3389 (RDP)' : '22 (SSH)' }}</span>
                </div>
            </div>

            <!-- Connection Guide -->
            <div class="card">
                <div class="card-title">🔌 How to Connect</div>
                @if(strtolower($service->package->category ?? '') === 'rdp')
                    <p style="color: #94a3b8; font-size: 13px; margin: 0 0 8px 0;">
                        Open <strong>Remote Desktop Connection (mstsc)</strong> on Windows or Mac, enter your Server IP and login with the credentials above:
                    </p>
                    <div class="code-box">{{ $service->ip_address }}</div>
                @else
                    <p style="color: #94a3b8; font-size: 13px; margin: 0 0 8px 0;">
                        Connect to your server via SSH terminal using the command below:
                    </p>
                    <div class="code-box">ssh {{ $username }}@{{ $service->ip_address }}</div>
                @endif
            </div>

            <!-- CTA Button -->
            <div class="btn-container">
                <a href="{{ url('/customer/services') }}" class="btn">
                    Manage VPS in Customer Portal &rarr;
                </a>
            </div>

            <p style="color: #64748b; font-size: 13px; text-align: center; margin-top: 20px;">
                💡 <em>Tip: You can reboot, shutdown, reset your password, and boot into rescue mode at any time inside your Customer Portal.</em>
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>Need help? Open a support ticket from your <a href="{{ url('/customer') }}">Customer Portal</a>.</p>
        </div>
    </div>
</body>
</html>
