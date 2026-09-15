<x-filament-panels::page>
    @php
        $service = $this->record;
        $package = $service->package;
        $specs = $service->specs_snapshot ?? (is_string($package->specs ?? null) ? json_decode($package->specs, true) : ($package->specs ?? []));
        $activeAddons = $service->active_addons ?? [];
        $password = $service->decrypted_password;
        $user = $service->default_user ?? 'root';
        $status = strtolower($this->liveStatus ?? $service->status ?? 'unknown');
        $isRdp = strtolower($package->category ?? '') === 'rdp';
        $port = $isRdp ? 3389 : 22;
        $unpaidInvoice = $service->order?->invoice;
        $isPendingInvoice = $unpaidInvoice && in_array($unpaidInvoice->status, ['pending', 'unpaid']);
        $isAwaiting = in_array($status, ['awaiting_provisioning', 'pending']);
        $isActive = in_array($status, ['running', 'active', 'ok']);
        $isSuspended = in_array($status, ['stopped', 'suspended']);
    @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .vortex-vps-container {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            color: #0F172A;
            display: flex !important;
            flex-direction: column !important;
            gap: 28px !important;
            width: 100% !important;
        }

        /* Guaranteed 28px separation between every card section */
        .vortex-vps-container > * {
            margin-bottom: 28px !important;
        }
        .vortex-vps-container > *:last-child {
            margin-bottom: 0 !important;
        }

        .vortex-mono {
            font-family: 'JetBrains Mono', 'Segoe UI Mono', Consolas, ui-monospace, monospace !important;
            font-feature-settings: "liga" 0;
            letter-spacing: -0.01em;
        }

        .vortex-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 22px;
            padding: 28px 32px;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
            transition: all 0.2s ease;
        }

        .vortex-alert-box {
            background: #FFFBEB;
            border: 1.5px solid #FDE68A;
            border-radius: 20px;
            padding: 20px 26px;
            box-shadow: 0 4px 16px -2px rgba(245, 158, 11, 0.08);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .vortex-split-top {
            display: grid;
            grid-template-columns: 1fr 1.15fr;
            gap: 24px;
            align-items: stretch;
        }

        @media (max-width: 1024px) {
            .vortex-split-top {
                grid-template-columns: 1fr;
            }
        }

        /* Server Identity Left Box */
        .vortex-server-hero {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            text-align: center;
            padding: 32px 28px;
        }

        .vortex-server-icon-large {
            width: 76px;
            height: 76px;
            border-radius: 22px;
            margin: 0 auto 18px auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Status Badge */
        .vortex-status-banner-active {
            background: #ECFDF5;
            color: #047857;
            border: 1.5px solid #A7F3D0;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 10px 20px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            margin-top: 20px;
        }

        .vortex-status-banner-awaiting {
            background: #FEF3C7;
            color: #B45309;
            border: 1.5px solid #FDE68A;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 10px 20px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            margin-top: 20px;
        }

        /* WHMCS Right Billing Table */
        .vortex-billing-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 14px;
        }

        .vortex-billing-table tr td {
            padding: 11px 14px;
            font-size: 13px;
            border-bottom: 1px solid #F1F5F9;
        }

        .vortex-billing-table tr:last-child td {
            border-bottom: none;
        }

        .vortex-billing-label {
            color: #64748B;
            font-weight: 600;
            width: 38%;
        }

        .vortex-billing-value {
            color: #0F172A;
            font-weight: 700;
            text-align: right;
        }

        /* Modern Tabs Navigation */
        .vortex-tabs-nav {
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px solid #F1F5F9;
            padding-bottom: 12px;
            margin-bottom: 24px;
            overflow-x: auto;
        }

        .vortex-tab-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            color: #64748B;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .vortex-tab-btn:hover {
            color: #0F172A;
            background: #F8FAFC;
        }

        .vortex-tab-btn.active {
            background: #FAF5FF;
            color: #673DE6;
            box-shadow: 0 2px 8px rgba(103, 61, 230, 0.12);
        }

        /* Power Control Action Pills */
        .vortex-action-pill {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            color: #334155;
            font-size: 12px;
            font-weight: 700;
            padding: 8px 14px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .vortex-action-pill:hover {
            background: #F8FAFC;
            border-color: #CBD5E1;
            color: #0F172A;
            transform: translateY(-1px);
        }

        .vortex-action-pill-danger {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #DC2626;
        }
        .vortex-action-pill-danger:hover {
            background: #FEE2E2;
            border-color: #FCA5A5;
            color: #B91C1C;
        }

        .vortex-cred-row {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .vortex-cred-row:last-child {
            margin-bottom: 0;
        }

        .vortex-stat-box {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.2s ease;
        }
        .vortex-stat-box:hover {
            border-color: #CBD5E1;
            background: #F1F5F9;
        }

        .vortex-stat-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: #FAF5FF;
            border: 1px solid #E9D5FF;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #673DE6;
        }

        .vortex-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
        }

        .vortex-grid-4 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .vortex-btn-primary {
            background: #673DE6;
            color: #FFFFFF !important;
            font-weight: 700;
            border-radius: 12px;
            padding: 10px 20px;
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
        }
        .vortex-btn-secondary:hover {
            background: #F8FAFC;
            border-color: #CBD5E1;
            color: #0F172A !important;
        }

        .vortex-icon-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 5px;
            border-radius: 8px;
            color: #64748B;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }
        .vortex-icon-btn:hover {
            background: #E2E8F0;
            color: #0F172A;
        }

        .vortex-badge-purple {
            background: #FAF5FF;
            color: #673DE6;
            border: 1px solid #E9D5FF;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
        }

        @media (max-width: 800px) {
            .vortex-grid-2, .vortex-grid-4 {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="vortex-vps-container" x-data="{ activeTab: 'info', showPassword: false, copied: false, copyText(text) { if (!text) return; navigator.clipboard.writeText(text); this.copied = true; setTimeout(() => this.copied = false, 2000); } }">

        <!-- Awaiting Provisioning / Unpaid Invoice Alert Banner -->
        @if($isAwaiting)
            <div class="vortex-alert-box">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div style="width: 46px; height: 46px; border-radius: 14px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #FDE68A;">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h4 style="font-size: 15px; font-weight: 800; color: #78350F; margin: 0;">Server Deployment Pending Verification</h4>
                        <p style="font-size: 13px; color: #92400E; margin: 4px 0 0 0; line-height: 1.5;">
                            @if($isPendingInvoice)
                                Invoice <strong class="vortex-mono" style="color: #78350F;">#{{ $unpaidInvoice->invoice_number }}</strong> (${{ number_format($unpaidInvoice->total, 2) }}) is currently unpaid. Complete payment to initiate automatic instance provisioning.
                            @else
                                Your payment proof has been submitted. Our engineering team is verifying the transaction hash to launch your server.
                            @endif
                        </p>
                    </div>
                </div>
                @if($isPendingInvoice)
                    <a href="{{ route('checkout.crypto-pay', $unpaidInvoice->id) }}" class="vortex-btn-primary" style="flex-shrink: 0;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span>Pay Invoice #{{ $unpaidInvoice->invoice_number }}</span>
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @endif
            </div>
        @endif

        <!-- SECTION 1: WHMCS Top Split Hero (Product Identity + Billing Summary) -->
        <div class="vortex-split-top">

            <!-- Left Box: Instance Identity & Status (WHMCS Left Card Standard) -->
            <div class="vortex-card vortex-server-hero">
                <div>
                    <!-- Server Icon -->
                    <div class="vortex-server-icon-large {{ $isActive ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : ($isSuspended ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-amber-50 text-amber-600 border border-amber-200') }}" style="{{ $isActive ? 'background: #ECFDF5; color: #059669; border: 1.5px solid #A7F3D0;' : ($isSuspended ? 'background: #FEF2F2; color: #DC2626; border: 1.5px solid #FECACA;' : 'background: #FFFBEB; color: #D97706; border: 1.5px solid #FDE68A;') }}">
                        <svg style="width: 38px; height: 38px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                    </div>

                    <!-- Server Title & Plan -->
                    <h2 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0; letter-spacing: -0.02em;">
                        {{ $service->formatted_hostname }}
                    </h2>
                    <p style="font-size: 13px; color: #64748B; margin: 6px 0 0 0;">
                        {{ $package->name ?? 'Cloud VPS' }} &bull; {{ $service->formatted_region }}
                    </p>

                    <!-- Status Banner -->
                    @if($isActive)
                        <div class="vortex-status-banner-active">
                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 9999px; background: #10B981;"></span>
                            ACTIVE INSTANCE
                        </div>
                    @elseif($isSuspended)
                        <div class="vortex-status-banner-awaiting" style="background: #FEF2F2; color: #DC2626; border-color: #FECACA;">
                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 9999px; background: #DC2626;"></span>
                            SUSPENDED
                        </div>
                    @else
                        <div class="vortex-status-banner-awaiting">
                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 9999px; background: #D97706;"></span>
                            AWAITING PROVISIONING
                        </div>
                    @endif
                </div>

                <!-- Quick Actions Strip -->
                <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #F1F5F9; display: flex; flex-wrap: wrap; justify-content: center; gap: 8px;">
                    @if($isActive)
                        <button type="button" wire:click="mountAction('restart')" class="vortex-action-pill" title="Reboot instance">
                            <svg style="width: 14px; height: 14px; color: #D97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Reboot
                        </button>
                        <button type="button" wire:click="mountAction('shutdown')" class="vortex-action-pill" title="Graceful shutdown">
                            <svg style="width: 14px; height: 14px; color: #64748B;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Shutdown
                        </button>
                        <button type="button" wire:click="mountAction('stop')" class="vortex-action-pill vortex-action-pill-danger" title="Power off">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Power Off
                        </button>
                    @endif
                    <button type="button" wire:click="mountAction('upgrade')" class="vortex-action-pill" style="border-color: #E9D5FF; color: #673DE6; background: #FAF5FF;">
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        Upgrade Server
                    </button>
                </div>
            </div>

            <!-- Right Box: WHMCS Billing & Subscription Table (1:1 WHMCS Match) -->
            <div class="vortex-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 14px; border-bottom: 1px solid #F1F5F9;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 36px; height: 36px; border-radius: 10px; background: #FAF5FF; color: #673DE6; display: flex; align-items: center; justify-content: center;">
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <h3 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0;">Billing & Service Summary</h3>
                        </div>
                        <span class="vortex-mono" style="font-size: 12px; font-weight: 700; color: #64748B;">
                            #NODE-{{ str_pad($service->id, 5, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>

                    <!-- WHMCS Billing Specification Rows -->
                    <table class="vortex-billing-table">
                        <tbody>
                            <tr>
                                <td class="vortex-billing-label">Registration Date</td>
                                <td class="vortex-billing-value">{{ $service->created_at ? $service->created_at->format('l, F jS, Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="vortex-billing-label">Recurring Amount</td>
                                <td class="vortex-billing-value" style="color: #059669; font-size: 15px;">
                                    <span class="vortex-mono">{{ $service->formatted_pricing }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="vortex-billing-label">Billing Cycle</td>
                                <td class="vortex-billing-value">{{ $service->formatted_billing_cycle }}</td>
                            </tr>
                            <tr>
                                <td class="vortex-billing-label">Next Due Date</td>
                                <td class="vortex-billing-value">
                                    {{ $service->next_due_date ? \Carbon\Carbon::parse($service->next_due_date)->format('l, F jS, Y') : 'Pending Renewal' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="vortex-billing-label">Payment Method</td>
                                <td class="vortex-billing-value">Cryptocurrency (Auto-Verified)</td>
                            </tr>
                            <tr>
                                <td class="vortex-billing-label">Order Number</td>
                                <td class="vortex-billing-value">
                                    <span class="vortex-mono" style="color: #673DE6;">#{{ $service->order?->order_number ?? 'N/A' }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Quick Navigation -->
                <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                    <a href="{{ url('/customer/invoices') }}" class="vortex-btn-secondary">
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        View Invoices
                    </a>
                    <a href="{{ url('/customer/support-tickets/create') }}" class="vortex-btn-primary" style="padding: 10px 18px; font-size: 12px;">
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Open Support Ticket
                    </a>
                </div>
            </div>

        </div>

        <!-- SECTION 2: WHMCS Interactive Tabs System (Clean Cosmic Violet SaaS) -->
        <div class="vortex-card" style="padding: 28px 32px;">

            <!-- Tabs Navigation Bar -->
            <div class="vortex-tabs-nav">
                <button type="button" @click="activeTab = 'info'" :class="{ 'active': activeTab === 'info' }" class="vortex-tab-btn">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    Server Information & Access
                </button>
                <button type="button" @click="activeTab = 'options'" :class="{ 'active': activeTab === 'options' }" class="vortex-tab-btn">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    Configurable Options
                </button>
                <button type="button" @click="activeTab = 'hardware'" :class="{ 'active': activeTab === 'hardware' }" class="vortex-tab-btn">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Hardware Resources & Telemetry
                </button>
            </div>

            <!-- TAB 1 CONTENT: Access & Connection Credentials -->
            <div x-show="activeTab === 'info'" class="vortex-grid-2" style="align-items: stretch;">
                
                <!-- Left: Credential Fields -->
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                        <h4 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0;">Connection Credentials</h4>
                        <span style="font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 6px; background: #F1F5F9; color: #475569;">
                            {{ $isRdp ? 'RDP Remote Desktop' : 'SSH Terminal' }}
                        </span>
                    </div>

                    <div class="vortex-cred-row">
                        <span style="font-size: 13px; font-weight: 600; color: #64748B;">Primary IPv4</span>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="vortex-mono" style="font-size: 13px; font-weight: 700; color: {{ !empty($service->ip_address) ? '#0F172A' : '#94A3B8' }};">
                                {{ $service->ip_address ?: 'Pending Assignment' }}
                            </span>
                            @if(!empty($service->ip_address))
                                <button @click="copyText('{{ $service->ip_address }}')" type="button" class="vortex-icon-btn" title="Copy IP">
                                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="vortex-cred-row">
                        <span style="font-size: 13px; font-weight: 600; color: #64748B;">Username</span>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="vortex-mono" style="font-size: 13px; font-weight: 700; color: #0F172A;">{{ $user }}</span>
                            <button @click="copyText('{{ $user }}')" type="button" class="vortex-icon-btn" title="Copy Username">
                                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            </button>
                        </div>
                    </div>

                    <div class="vortex-cred-row">
                        <span style="font-size: 13px; font-weight: 600; color: #64748B;">Root Password</span>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            @if(!empty($password))
                                <span class="vortex-mono" style="font-size: 13px; font-weight: 700; color: #0F172A;" x-text="showPassword ? '{{ addslashes($password) }}' : '••••••••••••••••'"></span>
                                <button @click="showPassword = !showPassword" type="button" class="vortex-icon-btn" title="Toggle Visibility">
                                    <svg x-show="!showPassword" style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <svg x-show="showPassword" style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                                </button>
                                <button @click="copyText('{{ addslashes($password) }}')" type="button" class="vortex-icon-btn" title="Copy Password">
                                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </button>
                            @else
                                <span class="vortex-mono" style="font-size: 12px; color: #94A3B8;">Generated upon activation</span>
                            @endif
                        </div>
                    </div>

                    <div class="vortex-cred-row">
                        <span style="font-size: 13px; font-weight: 600; color: #64748B;">Default Port</span>
                        <span class="vortex-mono" style="font-size: 13px; font-weight: 700; color: #0F172A;">{{ $port }}</span>
                    </div>
                </div>

                <!-- Right: Quick Terminal Connect Console -->
                <div style="display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                            <h4 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0;">Quick Terminal Connect</h4>
                            @if(!empty($service->ip_address))
                                <span class="vortex-mono" style="font-size: 11px; color: #10B981; font-weight: 700;">● PORT {{ $port }} OPEN</span>
                            @else
                                <span style="font-size: 11px; color: #94A3B8; font-weight: 600;">Awaiting IP allocation</span>
                            @endif
                        </div>

                        <div style="padding: 16px 20px; border-radius: 14px; background: #0F172A; border: 1px solid #1E293B;">
                            @if(!empty($service->ip_address))
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="display: flex; align-items: center; gap: 10px; overflow: hidden;">
                                        <span style="color: #673DE6; font-weight: 700;" class="vortex-mono">$</span>
                                        <span class="vortex-mono" style="font-size: 13px; color: #34D399; font-weight: 600;">ssh {{ $user }}@{{ $service->ip_address }} -p {{ $port }}</span>
                                    </div>
                                    <button @click="copyText('ssh {{ $user }}@{{ $service->ip_address }} -p {{ $port }}')" type="button" class="vortex-icon-btn" style="color: #94A3B8;" title="Copy SSH Command">
                                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    </button>
                                </div>
                            @else
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="color: #64748B; font-weight: 700;" class="vortex-mono">$</span>
                                    <span class="vortex-mono" style="font-size: 12px; color: #64748B;">ssh command will be active upon IP assignment</span>
                                </div>
                            @endif
                        </div>

                        <p style="font-size: 12px; color: #64748B; margin-top: 12px; line-height: 1.6;">
                            Connect from your terminal using standard OpenSSH or Putty. Use root credentials or your registered SSH public key for instant shell access.
                        </p>
                    </div>

                    <div style="display: flex; align-items: center; gap: 10px; margin-top: 20px;">
                        @if($isActive)
                            <button type="button" wire:click="mountAction('reset_password')" class="vortex-action-pill" style="font-size: 12px;">
                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                Reset Root Password
                            </button>
                            <button type="button" wire:click="mountAction('rescue')" class="vortex-action-pill vortex-action-pill-danger" style="font-size: 12px;">
                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                Rescue Mode
                            </button>
                        @endif
                    </div>
                </div>

            </div>

            <!-- TAB 2 CONTENT: Configurable Options (1:1 WHMCS Screenshot #2 Match) -->
            <div x-show="activeTab === 'options'">
                <div style="margin-bottom: 16px;">
                    <h4 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0;">Purchased Instance Configuration</h4>
                    <p style="font-size: 13px; color: #64748B; margin: 4px 0 0 0;">All hardware options, software stacks, and cloud add-ons selected during order checkout.</p>
                </div>

                <table class="vortex-billing-table">
                    <tbody>
                        <tr>
                            <td class="vortex-billing-label">Operating System</td>
                            <td class="vortex-billing-value" style="font-weight: 800;">
                                {{ $specs['os'] ?? ($service->os_image ?? 'Ubuntu 24.04 LTS') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="vortex-billing-label">Server Location / Datacenter</td>
                            <td class="vortex-billing-value">
                                {{ $service->formatted_region }}
                            </td>
                        </tr>
                        <tr>
                            <td class="vortex-billing-label">Hardware Architecture</td>
                            <td class="vortex-billing-value">Enterprise KVM Cloud Instance</td>
                        </tr>
                        <tr>
                            <td class="vortex-billing-label">Dedicated IPv4</td>
                            <td class="vortex-billing-value">1 Dedicated Primary IPv4 (Included)</td>
                        </tr>
                        <tr>
                            <td class="vortex-billing-label">Management Level</td>
                            <td class="vortex-billing-value">Unmanaged (Full Root / Sudo Control)</td>
                        </tr>
                        <tr>
                            <td class="vortex-billing-label">DDoS Protection</td>
                            <td class="vortex-billing-value" style="color: #059669;">
                                Automated Upstream 3.2 Tbps Scrubbing Filter
                            </td>
                        </tr>
                        @if(!empty($activeAddons) && count($activeAddons) > 0)
                            <tr>
                                <td class="vortex-billing-label">Active Cloud Add-Ons</td>
                                <td class="vortex-billing-value">
                                    <div style="display: flex; flex-wrap: wrap; gap: 6px; justify-content: flex-end;">
                                        @foreach($activeAddons as $addon)
                                            <span class="vortex-badge-purple" style="font-size: 11px;">
                                                <svg style="width: 12px; height: 12px; display: inline-block; vertical-align: middle; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                {{ $addon['name'] ?? 'Addon' }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- TAB 3 CONTENT: Hardware Resources & Telemetry (Virtualizor Modernized) -->
            <div x-show="activeTab === 'hardware'">
                <div style="margin-bottom: 20px;">
                    <h4 style="font-size: 15px; font-weight: 800; color: #0F172A; margin: 0;">Allocated Hardware Telemetry</h4>
                    <p style="font-size: 13px; color: #64748B; margin: 4px 0 0 0;">Dedicated physical bare-metal hardware resources provisioned for this virtual instance.</p>
                </div>

                <div class="vortex-grid-4">
                    <!-- Compute Cores -->
                    <div class="vortex-stat-box">
                        <div class="vortex-stat-icon-wrapper">
                            <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                        </div>
                        <div>
                            <div style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.04em;">Compute Cores</div>
                            <div style="font-size: 17px; font-weight: 800; color: #0F172A; margin-top: 3px;">{{ $specs['cores'] ?? '4 vCPU' }}</div>
                        </div>
                    </div>

                    <!-- RAM Memory -->
                    <div class="vortex-stat-box">
                        <div class="vortex-stat-icon-wrapper">
                            <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <div>
                            <div style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.04em;">RAM Memory</div>
                            <div style="font-size: 17px; font-weight: 800; color: #0F172A; margin-top: 3px;">{{ $specs['memory'] ?? '8 GB' }}</div>
                        </div>
                    </div>

                    <!-- NVMe Storage -->
                    <div class="vortex-stat-box">
                        <div class="vortex-stat-icon-wrapper">
                            <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                        </div>
                        <div>
                            <div style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.04em;">NVMe Storage</div>
                            <div style="font-size: 17px; font-weight: 800; color: #0F172A; margin-top: 3px;">{{ $specs['storage'] ?? '100 GB NVMe' }}</div>
                        </div>
                    </div>

                    <!-- Port Speed -->
                    <div class="vortex-stat-box">
                        <div class="vortex-stat-icon-wrapper">
                            <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <div style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.04em;">Port Speed</div>
                            <div style="font-size: 17px; font-weight: 800; color: #0F172A; margin-top: 3px;">{{ $specs['bandwidth'] ?? '1 Gbps' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Telemetry Status Bar -->
                <div style="margin-top: 24px; padding: 18px 24px; border-radius: 16px; background: #F8FAFC; border: 1px solid #E2E8F0; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 14px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="position: relative; display: flex; height: 10px; width: 10px;">
                            <span style="position: absolute; display: inline-flex; height: 100%; width: 100%; border-radius: 9999px; background: #10B981; opacity: 0.75; animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
                            <span style="position: relative; display: inline-flex; border-radius: 9999px; height: 10px; width: 10px; background: #059669;"></span>
                        </span>
                        <span style="font-size: 13px; font-weight: 700; color: #0F172A;">Enterprise Telemetry Node Online</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 20px; font-size: 12px; color: #64748B;">
                        <span>Uptime Guarantee: <strong style="color: #059669;">99.99%</strong></span>
                        <span>Hypervisor: <strong style="color: #0F172A;">KVM-QEMU Enterprise</strong></span>
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-filament-panels::page>
