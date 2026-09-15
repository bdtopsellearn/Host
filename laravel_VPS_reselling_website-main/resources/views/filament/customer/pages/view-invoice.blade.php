<x-filament-panels::page>
    @php
        $invoice = $this->record;
        $order = $invoice->order;
        $user = $invoice->user ?? auth()->user();
        $isPaid = $invoice->status === 'paid';
        $isUnpaid = in_array($invoice->status, ['pending', 'unpaid']);
        $orderItems = $order?->items ?? collect();
        $services = $order?->services ?? collect();
    @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .vortex-invoice-wrapper {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            color: #0F172A;
            max-width: 960px;
            margin: 0 auto;
            width: 100%;
        }

        .vortex-mono {
            font-family: 'JetBrains Mono', 'Segoe UI Mono', Consolas, monospace !important;
            font-feature-settings: "liga" 0;
        }

        /* Printable Paper Document Container */
        .vortex-invoice-sheet {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 24px;
            padding: 44px 48px;
            box-shadow: 0 10px 30px -4px rgba(15, 23, 42, 0.05);
            position: relative;
        }

        /* Top Header Grid */
        .vortex-invoice-header {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
            padding-bottom: 28px;
            border-bottom: 1.5px solid #F1F5F9;
        }

        /* 3-Pillar Highlight Metric Strip (WHMCS Modernized) */
        .vortex-metric-strip {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin: 28px 0;
        }

        .vortex-metric-card {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 16px 20px;
        }

        .vortex-metric-card.highlight {
            background: #FAF5FF;
            border-color: #E9D5FF;
        }

        /* Customer & Vendor Address Grid */
        .vortex-parties-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            padding-bottom: 28px;
            border-bottom: 1px solid #F1F5F9;
            margin-bottom: 28px;
        }

        /* Line Items Table */
        .vortex-items-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 28px;
        }

        .vortex-items-table th {
            background: #F8FAFC;
            color: #475569;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 14px 18px;
            text-align: left;
            border-top: 1px solid #E2E8F0;
            border-bottom: 1px solid #E2E8F0;
        }

        .vortex-items-table th:first-child {
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
            border-left: 1px solid #E2E8F0;
        }

        .vortex-items-table th:last-child {
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
            border-right: 1px solid #E2E8F0;
            text-align: right;
        }

        .vortex-items-table td {
            padding: 18px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: top;
            font-size: 13px;
        }

        .vortex-items-table td:last-child {
            text-align: right;
        }

        /* Financial Calculation Box */
        .vortex-totals-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 32px;
        }

        .vortex-totals-box {
            width: 340px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 18px;
            padding: 20px 24px;
        }

        .vortex-total-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 13px;
            color: #64748B;
        }

        .vortex-total-row.grand-total {
            border-top: 1.5px solid #E2E8F0;
            margin-top: 8px;
            padding-top: 12px;
            font-size: 18px;
            font-weight: 800;
            color: #0F172A;
        }

        /* Status Pills */
        .vortex-status-pill-unpaid {
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

        .vortex-status-pill-paid {
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

        .vortex-btn-primary {
            background: #673DE6;
            color: #FFFFFF !important;
            font-weight: 700;
            border-radius: 12px;
            padding: 11px 22px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            box-shadow: 0 4px 14px 0 rgba(103, 61, 230, 0.25);
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }
        .vortex-btn-primary:hover {
            background: #5428D8;
            transform: translateY(-1px);
        }

        .vortex-btn-secondary {
            background: #FFFFFF;
            color: #334155 !important;
            font-weight: 700;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 10px 18px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .vortex-btn-secondary:hover {
            background: #F8FAFC;
            border-color: #CBD5E1;
            color: #0F172A !important;
        }

        /* Print Media Styles: Clean White Paper Output */
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 15mm;
            }

            html, body {
                height: auto !important;
                min-height: 0 !important;
                overflow: visible !important;
                background: #FFFFFF !important;
                color: #0F172A !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Hide all Filament wrappers & chrome */
            .fi-topbar,
            .fi-sidebar,
            .fi-sidebar-ctn,
            .fi-header,
            .fi-header-main-ctn,
            .fi-page-header-main-ctn,
            .fi-breadcrumbs,
            .vortex-no-print {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                max-height: 0 !important;
                overflow: hidden !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Reset all layout containers to static document flow */
            .fi-layout,
            .fi-main-ctn,
            .fi-main,
            .fi-page,
            .fi-page-main,
            .fi-page-content,
            .vortex-invoice-wrapper {
                display: block !important;
                position: static !important;
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
                min-height: 0 !important;
                overflow: visible !important;
                margin: 0 !important;
                padding: 0 !important;
                transform: none !important;
                float: none !important;
            }

            .vortex-invoice-sheet {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }

        @media (max-width: 768px) {
            .vortex-invoice-sheet {
                padding: 24px 20px;
            }
            .vortex-metric-strip {
                grid-template-columns: 1fr;
            }
            .vortex-parties-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .vortex-totals-box {
                width: 100%;
            }
        }
    </style>

    <div class="vortex-invoice-wrapper space-y-6">

        <!-- Unpaid Quick Alert Bar (Screen Only) -->
        @if($isUnpaid)
            <div class="vortex-no-print" style="background: #FFFBEB; border: 1.5px solid #FDE68A; border-radius: 18px; padding: 18px 24px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 24px;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 42px; height: 42px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #FDE68A;">
                        <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h4 style="font-size: 14px; font-weight: 800; color: #78350F; margin: 0;">Payment Required</h4>
                        <p style="font-size: 13px; color: #92400E; margin: 2px 0 0 0;">This invoice is currently unpaid. Please complete payment before the due date to avoid service interruption.</p>
                    </div>
                </div>
                <a href="{{ route('checkout.crypto-pay', $invoice->id) }}" class="vortex-btn-primary" style="flex-shrink: 0;">
                    <span style="font-size: 15px;">🪙</span>
                    <span>Pay via Crypto Now</span>
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        @endif

        <!-- The Printable White Document Sheet -->
        <div class="vortex-invoice-sheet">

            <!-- 1. Header: Brand Logo + Status & Invoice Number -->
            <div class="vortex-invoice-header">
                <div>
                    <!-- Brand Identity -->
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

                <!-- Invoice Label & Badge -->
                <div style="text-align: right;">
                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 10px; margin-bottom: 6px;">
                        <h1 style="font-size: 26px; font-weight: 900; color: #0F172A; margin: 0; letter-spacing: -0.02em;">INVOICE</h1>
                        @if($isPaid)
                            <span class="vortex-status-pill-paid">● PAID</span>
                        @elseif($isUnpaid)
                            <span class="vortex-status-pill-unpaid">● UNPAID</span>
                        @else
                            <span style="background: #F1F5F9; color: #475569; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 9999px;">{{ strtoupper($invoice->status) }}</span>
                        @endif
                    </div>
                    <p class="vortex-mono" style="font-size: 15px; font-weight: 800; color: #673DE6; margin: 0;">
                        #{{ $invoice->invoice_number }}
                    </p>
                </div>
            </div>

            <!-- 2. WHMCS 3-Pillar Highlight Metric Strip -->
            <div class="vortex-metric-strip">
                <div class="vortex-metric-card">
                    <div style="font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em;">Invoice Date</div>
                    <div style="font-size: 15px; font-weight: 800; color: #0F172A; margin-top: 4px;">
                        {{ $invoice->created_at->format('M d, Y') }}
                    </div>
                    <div style="font-size: 11px; color: #94A3B8; margin-top: 2px;">{{ $invoice->created_at->format('l') }}</div>
                </div>

                <div class="vortex-metric-card">
                    <div style="font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em;">Payment Due Date</div>
                    <div style="font-size: 15px; font-weight: 800; color: {{ $isUnpaid ? '#DC2626' : '#0F172A' }}; margin-top: 4px;">
                        {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') : 'Immediate' }}
                    </div>
                    <div style="font-size: 11px; color: #94A3B8; margin-top: 2px;">
                        {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('l') : 'Standard Net-3' }}
                    </div>
                </div>

                <div class="vortex-metric-card highlight">
                    <div style="font-size: 11px; font-weight: 800; color: #673DE6; text-transform: uppercase; letter-spacing: 0.05em;">Total Balance Due</div>
                    <div class="vortex-mono" style="font-size: 18px; font-weight: 900; color: {{ $isPaid ? '#059669' : '#673DE6' }}; margin-top: 4px;">
                        ${{ number_format($invoice->total, 2) }} USD
                    </div>
                    <div style="font-size: 11px; color: #64748B; margin-top: 2px;">
                        {{ $isPaid ? 'Settled in Full' : 'Awaiting Payment' }}
                    </div>
                </div>
            </div>

            <!-- 3. Parties: Invoiced To & Pay To (Vendor) -->
            <div class="vortex-parties-grid">
                <div>
                    <h5 style="font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.06em; margin: 0 0 10px 0;">Invoiced To:</h5>
                    <div style="font-size: 15px; font-weight: 800; color: #0F172A;">{{ $user->name }}</div>
                    <div style="font-size: 13px; color: #475569; margin-top: 3px;">{{ $user->email }}</div>
                    <div style="font-size: 12px; color: #64748B; margin-top: 6px;">
                        Client ID: <strong class="vortex-mono" style="color: #0F172A;">#CLIENT-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</strong>
                    </div>
                    @if($order)
                        <div style="font-size: 12px; color: #64748B; margin-top: 3px;">
                            Order Reference: <strong class="vortex-mono" style="color: #673DE6;">#{{ $order->order_number }}</strong>
                        </div>
                    @endif
                </div>

                <div>
                    <h5 style="font-size: 11px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.06em; margin: 0 0 10px 0;">Pay To:</h5>
                    <div style="font-size: 15px; font-weight: 800; color: #0F172A;">VortexCloud Global Solutions Inc.</div>
                    <div style="font-size: 13px; color: #475569; margin-top: 3px;">Cloud Hosting & Bare Metal Operations</div>
                    <div style="font-size: 12px; color: #64748B; margin-top: 6px;">Support: <span style="color: #0F172A;">billing@vortexcloud.net</span></div>
                    <div style="font-size: 12px; color: #64748B; margin-top: 3px;">Settlement Gateway: <span style="color: #059669; font-weight: 700;">Instant Cryptocurrency (USDT / USDC)</span></div>
                </div>
            </div>

            <!-- 4. Itemized Breakdown Table (1:1 WHMCS Match) -->
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
                                    <div style="font-size: 15px; font-weight: 800; color: #0F172A;">
                                        {{ $pkg->name ?? 'High Performance VPS' }}
                                    </div>
                                    <div style="font-size: 12px; color: #64748B; margin-top: 4px; line-height: 1.6;">
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
                                    @if(!empty($item->addons) && is_array($item->addons) && count($item->addons) > 0)
                                        <div style="margin-top: 6px; display: flex; flex-wrap: wrap; gap: 4px;">
                                            @foreach($item->addons as $addon)
                                                <span style="font-size: 10px; font-weight: 700; background: #FAF5FF; color: #673DE6; border: 1px solid #E9D5FF; border-radius: 6px; padding: 2px 8px;">
                                                    + {{ is_array($addon) ? ($addon['name'] ?? 'Addon') : $addon }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td style="text-align: center; vertical-align: top;">
                                    <span style="font-size: 12px; font-weight: 700; background: #F1F5F9; color: #475569; padding: 4px 10px; border-radius: 8px;">
                                        {{ ucfirst($item->billing_cycle ?? 'Monthly') }}
                                    </span>
                                </td>
                                <td style="vertical-align: top;">
                                    <span class="vortex-mono" style="font-size: 15px; font-weight: 800; color: #0F172A;">
                                        ${{ number_format((float) ($item->price ?? $invoice->amount), 2) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @elseif($services->count() > 0)
                        @foreach($services as $srv)
                            @php
                                $pkg = $srv->package;
                            @endphp
                            <tr>
                                <td>
                                    <div style="font-size: 15px; font-weight: 800; color: #0F172A;">
                                        {{ $pkg->name ?? 'Virtual Private Server' }}
                                    </div>
                                    <div style="font-size: 12px; color: #64748B; margin-top: 4px; line-height: 1.6;">
                                        <span>Hostname: <strong class="vortex-mono" style="color: #0F172A;">{{ $srv->formatted_hostname }}</strong></span><br>
                                        <span>Datacenter: <strong style="color: #0F172A;">{{ $srv->formatted_region }}</strong></span><br>
                                        <span>Server Node: <strong class="vortex-mono" style="color: #0F172A;">#NODE-{{ str_pad($srv->id, 5, '0', STR_PAD_LEFT) }}</strong></span>
                                    </div>
                                </td>
                                <td style="text-align: center; vertical-align: top;">
                                    <span style="font-size: 12px; font-weight: 700; background: #F1F5F9; color: #475569; padding: 4px 10px; border-radius: 8px;">
                                        {{ $srv->formatted_billing_cycle }}
                                    </span>
                                </td>
                                <td style="vertical-align: top;">
                                    <span class="vortex-mono" style="font-size: 15px; font-weight: 800; color: #0F172A;">
                                        ${{ number_format((float) $invoice->amount, 2) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @elseif(empty($invoice->order_id) && empty($invoice->service_id))
                        <tr>
                            <td>
                                <div style="font-size: 15px; font-weight: 800; color: #0F172A;">💳 Account Credit & Balance Top-Up</div>
                                <div style="font-size: 12px; color: #64748B; margin-top: 4px; line-height: 1.6;">
                                    <span>Pre-funded account balance for automated cloud VPS renewals and instant service deployment.</span><br>
                                    <span>Wallet credit applied automatically upon blockchain confirmation · Never expires</span>
                                </div>
                            </td>
                            <td style="text-align: center; vertical-align: top;">
                                <span style="font-size: 12px; font-weight: 700; background: #FAF5FF; color: #673DE6; border: 1px solid #E9D5FF; padding: 4px 10px; border-radius: 8px;">
                                    Account Deposit
                                </span>
                            </td>
                            <td style="vertical-align: top;">
                                <span class="vortex-mono" style="font-size: 15px; font-weight: 800; color: #0F172A;">
                                    ${{ number_format((float) $invoice->amount, 2) }}
                                </span>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td>
                                <div style="font-size: 15px; font-weight: 800; color: #0F172A;">Cloud VPS Service Provisioning</div>
                                <div style="font-size: 12px; color: #64748B; margin-top: 4px;">High-speed cloud server instance deployment and network setup.</div>
                            </td>
                            <td style="text-align: center; vertical-align: top;">
                                <span style="font-size: 12px; font-weight: 700; background: #F1F5F9; color: #475569; padding: 4px 10px; border-radius: 8px;">Standard</span>
                            </td>
                            <td style="vertical-align: top;">
                                <span class="vortex-mono" style="font-size: 15px; font-weight: 800; color: #0F172A;">
                                    ${{ number_format((float) $invoice->amount, 2) }}
                                </span>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <!-- 5. Totals & Settlement Calculation -->
            <div class="vortex-totals-wrapper">
                <div class="vortex-totals-box">
                    <div class="vortex-total-row">
                        <span>Subtotal:</span>
                        <span class="vortex-mono" style="font-weight: 700; color: #0F172A;">${{ number_format($invoice->amount, 2) }} USD</span>
                    </div>
                    <div class="vortex-total-row">
                        <span>Tax / VAT (0.00%):</span>
                        <span class="vortex-mono">${{ number_format($invoice->tax ?? 0, 2) }} USD</span>
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

            <!-- 6. Payment Settlement & Transactions History (WHMCS Bottom Table) -->
            <div style="border-top: 1px solid #F1F5F9; padding-top: 24px; margin-top: 10px;">
                <h5 style="font-size: 12px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 12px 0;">
                    Payment Settlement & Transaction History
                </h5>

                @if($isPaid)
                    <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 14px; padding: 14px 18px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; font-size: 13px;">
                        <div>
                            <span style="color: #64748B;">Settlement Date:</span>
                            <strong style="color: #0F172A; margin-left: 4px;">{{ $invoice->paid_at ? $invoice->paid_at->format('M d, Y h:i A') : 'Completed' }}</strong>
                        </div>
                        <div>
                            <span style="color: #64748B;">Gateway:</span>
                            <strong style="color: #0F172A; margin-left: 4px;">{{ strtoupper($invoice->payment_method ?? 'CRYPTO') }}</strong>
                        </div>
                        @if(!empty($invoice->crypto_txid))
                            <div>
                                <span style="color: #64748B;">TxID / Hash:</span>
                                <strong class="vortex-mono" style="color: #673DE6; margin-left: 4px;">{{ substr($invoice->crypto_txid, 0, 10) }}...{{ substr($invoice->crypto_txid, -8) }}</strong>
                            </div>
                        @endif
                        <div>
                            <span style="color: #64748B;">Amount:</span>
                            <strong class="vortex-mono" style="color: #059669; margin-left: 4px;">${{ number_format($invoice->total, 2) }} USD</strong>
                        </div>
                    </div>
                @else
                    <div style="background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 14px; padding: 16px 20px; text-align: center; color: #64748B; font-size: 13px;">
                        <span>No settlement transactions recorded yet. Awaiting payment submission.</span>
                    </div>
                @endif
            </div>

            <!-- 7. Footer Note & Actions -->
            <div class="vortex-no-print" style="border-top: 1px solid #F1F5F9; padding-top: 24px; margin-top: 28px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px;">
                <div style="font-size: 12px; color: #94A3B8;">
                    Thank you for partnering with VortexCloud. For billing inquiries, open a support ticket.
                </div>

                <div style="display: flex; align-items: center; gap: 10px;">
                    <a href="{{ url('/customer/invoices') }}" class="vortex-btn-secondary">
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Back to Invoices
                    </a>
                    <a href="{{ route('customer.invoices.print', $invoice->id) }}" target="_blank" class="vortex-btn-secondary">
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print Invoice
                    </a>
                    @if($isUnpaid)
                        <a href="{{ route('checkout.crypto-pay', $invoice->id) }}" class="vortex-btn-primary">
                            <span style="font-size: 14px;">🪙</span>
                            Pay ${{ number_format($invoice->total, 2) }} via Crypto
                        </a>
                    @endif
                </div>
            </div>

        </div>

    </div>
</x-filament-panels::page>
