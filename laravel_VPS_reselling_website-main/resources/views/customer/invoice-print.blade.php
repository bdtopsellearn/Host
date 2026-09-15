<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }} - VortexCloud</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #F1F5F9;
            color: #0F172A;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            padding: 30px 16px;
        }

        .vortex-mono {
            font-family: 'JetBrains Mono', 'Segoe UI Mono', Consolas, monospace !important;
            font-feature-settings: "liga" 0;
        }

        /* Screen Action Bar (Hidden in Print) */
        .vortex-print-bar {
            max-width: 860px;
            margin: 0 auto 20px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #0F172A;
            color: #FFFFFF;
            padding: 12px 24px;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.2);
        }

        .vortex-bar-btn {
            background: #673DE6;
            color: #FFFFFF;
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 13px;
            padding: 9px 18px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .vortex-bar-btn:hover {
            background: #5428D8;
        }

        .vortex-bar-btn-secondary {
            background: rgba(255, 255, 255, 0.12);
            color: #FFFFFF;
            border: 1px solid rgba(255, 255, 255, 0.2);
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            padding: 9px 16px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .vortex-bar-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Printable Sheet */
        .vortex-sheet {
            max-width: 860px;
            margin: 0 auto;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 20px;
            padding: 44px 48px;
            box-shadow: 0 4px 24px -2px rgba(15, 23, 42, 0.06);
        }

        .vortex-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding-bottom: 24px;
            border-bottom: 1.5px solid #F1F5F9;
        }

        /* Status Pills */
        .pill-unpaid {
            background: #FEF2F2;
            color: #DC2626;
            border: 1.5px solid #FECACA;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 6px 14px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .pill-paid {
            background: #ECFDF5;
            color: #047857;
            border: 1.5px solid #A7F3D0;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 6px 14px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* 3-Pillar Highlight Metric Strip */
        .vortex-metric-strip {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin: 24px 0;
        }

        .vortex-metric-card {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            padding: 14px 18px;
        }

        .vortex-metric-card.highlight {
            background: #FAF5FF;
            border-color: #E9D5FF;
        }

        /* Address Grid */
        .vortex-parties-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            padding-bottom: 24px;
            border-bottom: 1px solid #F1F5F9;
            margin-bottom: 24px;
        }

        /* Table */
        .vortex-items-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 24px;
        }

        .vortex-items-table th {
            background: #F8FAFC;
            color: #475569;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 12px 16px;
            text-align: left;
            border-top: 1px solid #E2E8F0;
            border-bottom: 1px solid #E2E8F0;
        }
        .vortex-items-table th:first-child {
            border-left: 1px solid #E2E8F0;
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }
        .vortex-items-table th:last-child {
            border-right: 1px solid #E2E8F0;
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            text-align: right;
        }

        .vortex-items-table td {
            padding: 16px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: top;
            font-size: 13px;
        }
        .vortex-items-table td:last-child {
            text-align: right;
        }

        /* Totals Box */
        .vortex-totals-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 24px;
        }

        .vortex-totals-box {
            width: 320px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 18px 22px;
        }

        .vortex-total-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 13px;
            color: #64748B;
        }

        .vortex-total-row.grand-total {
            border-top: 1.5px solid #E2E8F0;
            margin-top: 8px;
            padding-top: 10px;
            font-size: 17px;
            font-weight: 800;
            color: #0F172A;
        }

        /* Print Media Overrides */
        @media print {
            body {
                background: #FFFFFF !important;
                padding: 0 !important;
            }
            .vortex-print-bar {
                display: none !important;
            }
            .vortex-sheet {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            @page {
                size: A4 portrait;
                margin: 12mm 15mm;
            }
        }
    </style>
</head>
<body>

    <!-- Screen Navigation & Print Trigger Bar -->
    <div class="vortex-print-bar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 32px; height: 32px; border-radius: 8px; background: #673DE6; display: flex; align-items: center; justify-content: center; color: #FFFFFF; font-weight: 900; font-size: 14px;">
                V
            </div>
            <span style="font-weight: 700; font-size: 14px;">Invoice #{{ $invoice->invoice_number }}</span>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <button type="button" onclick="window.print()" class="vortex-bar-btn">
                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print / Save PDF
            </button>
            <button type="button" onclick="window.close()" class="vortex-bar-btn-secondary">
                Close
            </button>
        </div>
    </div>

    @php
        $order = $invoice->order;
        $user = $invoice->user;
        $isPaid = $invoice->status === 'paid';
        $isUnpaid = in_array($invoice->status, ['pending', 'unpaid']);
        $orderItems = $order?->items ?? collect();
        $services = $order?->services ?? collect();
    @endphp

    <!-- Pristine Printable Document Sheet -->
    <div class="vortex-sheet">

        <!-- 1. Header: Brand + Status -->
        <div class="vortex-header">
            <div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: #673DE6; display: flex; align-items: center; justify-content: center; color: #FFFFFF;">
                        <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <span style="font-size: 20px; font-weight: 900; letter-spacing: -0.03em; color: #0F172A;">VORTEX<span style="color: #673DE6;">CLOUD</span></span>
                </div>
                <p style="font-size: 12px; color: #64748B; margin: 6px 0 0 0; font-weight: 500;">
                    Enterprise Cloud Infrastructure & Managed VPS Solutions
                </p>
            </div>

            <div style="text-align: right;">
                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 10px; margin-bottom: 4px;">
                    <h1 style="font-size: 24px; font-weight: 900; color: #0F172A; margin: 0; letter-spacing: -0.02em;">INVOICE</h1>
                    @if($isPaid)
                        <span class="pill-paid">● PAID</span>
                    @elseif($isUnpaid)
                        <span class="pill-unpaid">● UNPAID</span>
                    @else
                        <span style="background: #F1F5F9; color: #475569; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 9999px;">{{ strtoupper($invoice->status) }}</span>
                    @endif
                </div>
                <p class="vortex-mono" style="font-size: 15px; font-weight: 800; color: #673DE6; margin: 0;">
                    #{{ $invoice->invoice_number }}
                </p>
            </div>
        </div>

        <!-- 2. Highlight Metric Strip -->
        <div class="vortex-metric-strip">
            <div class="vortex-metric-card">
                <div style="font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em;">Invoice Date</div>
                <div style="font-size: 14px; font-weight: 800; color: #0F172A; margin-top: 3px;">
                    {{ $invoice->created_at->format('M d, Y') }}
                </div>
                <div style="font-size: 11px; color: #94A3B8; margin-top: 2px;">{{ $invoice->created_at->format('l') }}</div>
            </div>

            <div class="vortex-metric-card">
                <div style="font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em;">Payment Due Date</div>
                <div style="font-size: 14px; font-weight: 800; color: {{ $isUnpaid ? '#DC2626' : '#0F172A' }}; margin-top: 3px;">
                    {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') : 'Immediate' }}
                </div>
                <div style="font-size: 11px; color: #94A3B8; margin-top: 2px;">
                    {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('l') : 'Standard Net-3' }}
                </div>
            </div>

            <div class="vortex-metric-card highlight">
                <div style="font-size: 11px; font-weight: 800; color: #673DE6; text-transform: uppercase; letter-spacing: 0.05em;">Total Balance Due</div>
                <div class="vortex-mono" style="font-size: 17px; font-weight: 900; color: {{ $isPaid ? '#059669' : '#673DE6' }}; margin-top: 3px;">
                    ${{ number_format($invoice->total, 2) }} USD
                </div>
                <div style="font-size: 11px; color: #64748B; margin-top: 2px;">
                    {{ $isPaid ? 'Settled in Full' : 'Awaiting Payment' }}
                </div>
            </div>
        </div>

        <!-- 3. Parties -->
        <div class="vortex-parties-grid">
            <div>
                <h5 style="font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.06em; margin: 0 0 8px 0;">Invoiced To:</h5>
                <div style="font-size: 14px; font-weight: 800; color: #0F172A;">{{ $user->name ?? 'Valued Client' }}</div>
                <div style="font-size: 13px; color: #475569; margin-top: 2px;">{{ $user->email ?? 'N/A' }}</div>
                <div style="font-size: 12px; color: #64748B; margin-top: 4px;">
                    Client ID: <strong class="vortex-mono" style="color: #0F172A;">#CLIENT-{{ str_pad($user->id ?? 1, 5, '0', STR_PAD_LEFT) }}</strong>
                </div>
                @if($order)
                    <div style="font-size: 12px; color: #64748B; margin-top: 2px;">
                        Order: <strong class="vortex-mono" style="color: #673DE6;">#{{ $order->order_number }}</strong>
                    </div>
                @endif
            </div>

            <div>
                <h5 style="font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.06em; margin: 0 0 8px 0;">Pay To:</h5>
                <div style="font-size: 14px; font-weight: 800; color: #0F172A;">VortexCloud Global Solutions Inc.</div>
                <div style="font-size: 13px; color: #475569; margin-top: 2px;">Cloud Hosting & Infrastructure Network</div>
                <div style="font-size: 12px; color: #64748B; margin-top: 4px;">Support: <span style="color: #0F172A;">billing@vortexcloud.net</span></div>
                <div style="font-size: 12px; color: #64748B; margin-top: 2px;">Gateway: <span style="color: #059669; font-weight: 700;">Instant Verified</span></div>
            </div>
        </div>

        <!-- 4. Line Items Table -->
        <table class="vortex-items-table">
            <thead>
                <tr>
                    <th style="width: 60%;">Description & Specifications</th>
                    <th style="width: 20%; text-align: center;">Billing Cycle</th>
                    <th style="width: 20%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @if($orderItems->count() > 0)
                    @foreach($orderItems as $item)
                        @php
                            $pkg = $item->package;
                            $specs = is_string($pkg?->specs) ? json_decode($pkg->specs, true) : ($pkg?->specs ?? []);
                        @endphp
                        <tr>
                            <td>
                                <div style="font-size: 14px; font-weight: 800; color: #0F172A;">
                                    {{ $pkg->name ?? 'High Performance VPS' }}
                                </div>
                                <div style="font-size: 12px; color: #64748B; margin-top: 4px; line-height: 1.5;">
                                    @if(!empty($item->hostname))
                                        <span>Hostname: <strong class="vortex-mono" style="color: #0F172A;">{{ $item->hostname }}</strong></span><br>
                                    @endif
                                    @if(!empty($item->os))
                                        <span>Operating System: <strong style="color: #0F172A;">{{ $item->os }}</strong></span><br>
                                    @endif
                                    @if(!empty($item->datacenter))
                                        <span>Datacenter: <strong style="color: #0F172A;">{{ $item->datacenter }}</strong></span><br>
                                    @endif
                                    @if(!empty($specs))
                                        <span>Hardware: <strong>{{ $specs['cores'] ?? 'vCPU' }} Cores / {{ $specs['memory'] ?? 'RAM' }} / {{ $specs['storage'] ?? 'Storage' }}</strong></span>
                                    @endif
                                </div>
                            </td>
                            <td style="text-align: center; vertical-align: top;">
                                <span style="font-size: 12px; font-weight: 700; background: #F1F5F9; color: #475569; padding: 4px 10px; border-radius: 8px;">
                                    {{ ucfirst($item->billing_cycle ?? 'Monthly') }}
                                </span>
                            </td>
                            <td style="vertical-align: top;">
                                <span class="vortex-mono" style="font-size: 14px; font-weight: 800; color: #0F172A;">
                                    ${{ number_format((float) ($item->price ?? $invoice->amount), 2) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @elseif($services->count() > 0)
                    @foreach($services as $srv)
                        <tr>
                            <td>
                                <div style="font-size: 14px; font-weight: 800; color: #0F172A;">
                                    {{ $srv->package->name ?? 'Virtual Private Server' }}
                                </div>
                                <div style="font-size: 12px; color: #64748B; margin-top: 4px; line-height: 1.5;">
                                    <span>Hostname: <strong class="vortex-mono" style="color: #0F172A;">{{ $srv->formatted_hostname }}</strong></span><br>
                                    <span>Datacenter: <strong style="color: #0F172A;">{{ $srv->formatted_region }}</strong></span><br>
                                    <span>Server ID: <strong class="vortex-mono" style="color: #0F172A;">#NODE-{{ str_pad($srv->id, 5, '0', STR_PAD_LEFT) }}</strong></span>
                                </div>
                            </td>
                            <td style="text-align: center; vertical-align: top;">
                                <span style="font-size: 12px; font-weight: 700; background: #F1F5F9; color: #475569; padding: 4px 10px; border-radius: 8px;">
                                    {{ $srv->formatted_billing_cycle }}
                                </span>
                            </td>
                            <td style="vertical-align: top;">
                                <span class="vortex-mono" style="font-size: 14px; font-weight: 800; color: #0F172A;">
                                    ${{ number_format((float) $invoice->amount, 2) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @elseif(empty($invoice->order_id) && empty($invoice->service_id))
                    <tr>
                        <td>
                            <div style="font-size: 14px; font-weight: 800; color: #0F172A;">💳 Account Credit & Balance Top-Up</div>
                            <div style="font-size: 12px; color: #64748B; margin-top: 3px; line-height: 1.5;">
                                <span>Pre-funded account balance for zero-downtime automated VPS renewals and instant service deployment.</span><br>
                                <span>Wallet credit applied automatically upon blockchain confirmation · Never expires</span>
                            </div>
                        </td>
                        <td style="text-align: center; vertical-align: top;">
                            <span style="font-size: 12px; font-weight: 700; background: #FAF5FF; color: #673DE6; border: 1px solid #E9D5FF; padding: 4px 10px; border-radius: 8px;">
                                Account Deposit
                            </span>
                        </td>
                        <td style="vertical-align: top;">
                            <span class="vortex-mono" style="font-size: 14px; font-weight: 800; color: #0F172A;">
                                ${{ number_format((float) $invoice->amount, 2) }}
                            </span>
                        </td>
                    </tr>
                @else
                    <tr>
                        <td>
                            <div style="font-size: 14px; font-weight: 800; color: #0F172A;">Cloud VPS Service Provisioning</div>
                            <div style="font-size: 12px; color: #64748B; margin-top: 3px;">High-speed cloud server instance deployment and network allocation.</div>
                        </td>
                        <td style="text-align: center; vertical-align: top;">
                            <span style="font-size: 12px; font-weight: 700; background: #F1F5F9; color: #475569; padding: 4px 10px; border-radius: 8px;">Standard</span>
                        </td>
                        <td style="vertical-align: top;">
                            <span class="vortex-mono" style="font-size: 14px; font-weight: 800; color: #0F172A;">
                                ${{ number_format((float) $invoice->amount, 2) }}
                            </span>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- 5. Totals Box -->
        <div class="vortex-totals-wrapper">
            <div class="vortex-totals-box">
                <div class="vortex-total-row">
                    <span>Subtotal:</span>
                    <span class="vortex-mono" style="font-weight: 700; color: #0F172A;">${{ number_format($invoice->amount, 2) }} USD</span>
                </div>
                <div class="vortex-total-row">
                    <span>Tax / VAT (0%):</span>
                    <span class="vortex-mono">$0.00 USD</span>
                </div>
                <div class="vortex-total-row">
                    <span>Credit Applied:</span>
                    <span class="vortex-mono">$0.00 USD</span>
                </div>
                <div class="vortex-total-row grand-total">
                    <span>Total Due:</span>
                    <span class="vortex-mono" style="color: {{ $isPaid ? '#059669' : '#673DE6' }};">${{ number_format($invoice->total, 2) }} USD</span>
                </div>
            </div>
        </div>

        <!-- 6. Payment & Settlement History -->
        <div style="border-top: 1px solid #F1F5F9; padding-top: 20px; margin-top: 8px;">
            <h5 style="font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 10px 0;">
                Payment Settlement & Verification
            </h5>

            @if($isPaid)
                <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 12px 16px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; font-size: 12px;">
                    <div>
                        <span style="color: #64748B;">Date:</span>
                        <strong style="color: #0F172A; margin-left: 4px;">{{ $invoice->paid_at ? $invoice->paid_at->format('M d, Y h:i A') : 'Completed' }}</strong>
                    </div>
                    <div>
                        <span style="color: #64748B;">Gateway:</span>
                        <strong style="color: #0F172A; margin-left: 4px;">{{ strtoupper($invoice->payment_method ?? 'CRYPTO') }}</strong>
                    </div>
                    @if(!empty($invoice->crypto_txid))
                        <div>
                            <span style="color: #64748B;">TxID:</span>
                            <strong class="vortex-mono" style="color: #673DE6; margin-left: 4px;">{{ substr($invoice->crypto_txid, 0, 10) }}...{{ substr($invoice->crypto_txid, -8) }}</strong>
                        </div>
                    @endif
                    <div>
                        <span style="color: #64748B;">Amount:</span>
                        <strong class="vortex-mono" style="color: #059669; margin-left: 4px;">${{ number_format($invoice->total, 2) }} USD</strong>
                    </div>
                </div>
            @else
                <div style="background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 12px; padding: 14px 18px; text-align: center; color: #64748B; font-size: 12px;">
                    <span>No settlement transactions recorded yet. Awaiting payment submission.</span>
                </div>
            @endif
        </div>

        <div style="border-top: 1px solid #F1F5F9; padding-top: 20px; margin-top: 24px; text-align: center; font-size: 11px; color: #94A3B8;">
            Thank you for choosing VortexCloud Infrastructure. This document is a computer-generated tax invoice.
        </div>

    </div>

    <!-- Automatically Trigger Print Dialog on Document Load -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Auto open print dialog after fonts render
            setTimeout(() => {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>
