<x-filament-panels::page>
    @php
        $user = auth()->user();
        $balance = $user->credit_balance;
        $invoices = $this->getDepositInvoices();
        $paidDeposits = $invoices->where('status', 'paid');
        $unpaidDeposits = $invoices->where('status', 'unpaid');
    @endphp

    <style>
        .vt-credit-wrap {
            font-family: inherit;
            display: flex;
            flex-direction: column;
            gap: 24px;
            width: 100%;
        }

        /* 1. HERO BALANCE CARD */
        .vt-balance-hero {
            background: linear-gradient(135deg, #16002C 0%, #2A084E 50%, #1A0038 100%);
            border-radius: 20px;
            padding: 32px 36px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 12px 32px -4px rgba(22, 0, 44, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
        .vt-balance-glow {
            position: absolute;
            top: -60px;
            right: -60px;
            width: 240px;
            height: 240px;
            background: radial-gradient(circle, rgba(103, 61, 230, 0.5) 0%, rgba(103, 61, 230, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .vt-balance-top {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }
        .vt-balance-label {
            font-size: 13px;
            font-weight: 700;
            color: #c4b5fd;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .vt-balance-badge {
            background: rgba(16, 185, 129, 0.18);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #34d399;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 0.03em;
        }
        .vt-balance-amount {
            font-size: 46px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #ffffff;
            line-height: 1;
            margin-bottom: 12px;
            display: flex;
            align-items: baseline;
            gap: 8px;
        }
        .vt-balance-currency {
            font-size: 18px;
            font-weight: 600;
            color: #a78bfa;
        }
        .vt-balance-desc {
            font-size: 13px;
            color: #e2e8f0;
            line-height: 1.5;
            max-width: 640px;
        }
        .vt-balance-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 28px;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .vt-stat-item {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }
        .vt-stat-label {
            font-size: 11px;
            color: #c4b5fd;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .vt-stat-val {
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
        }

        /* 2. TWO-COLUMN INTERACTIVE STAGE */
        .vt-credit-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }
        @media (min-width: 1024px) {
            .vt-credit-grid {
                grid-template-columns: 1.6fr 1fr;
            }
        }

        .vt-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            padding: 24px 28px;
        }
        .vt-card-head {
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .vt-card-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 4px 0;
        }
        .vt-card-subtitle {
            font-size: 12.5px;
            color: #64748b;
            margin: 0;
        }

        /* PRESET CHIPS */
        .vt-presets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
            gap: 10px;
            margin-bottom: 18px;
        }
        .vt-preset-btn {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 10px;
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            cursor: pointer;
            text-align: center;
            transition: all 0.15s ease;
        }
        .vt-preset-btn:hover {
            border-color: #673DE6;
            background: #faf8ff;
            color: #673DE6;
        }
        .vt-preset-active {
            background: #673DE6 !important;
            border-color: #673DE6 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(103, 61, 230, 0.3);
        }

        /* CUSTOM AMOUNT INPUT */
        .vt-input-group {
            margin-bottom: 20px;
        }
        .vt-input-label {
            font-size: 12px;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 6px;
            display: block;
        }
        .vt-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .vt-input-prefix {
            position: absolute;
            left: 16px;
            font-size: 18px;
            font-weight: 700;
            color: #64748b;
            pointer-events: none;
        }
        .vt-input-field {
            width: 100%;
            padding: 13px 16px 13px 36px;
            border-radius: 12px;
            border: 1.5px solid #cbd5e1;
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            outline: none;
            transition: all 0.15s ease;
        }
        .vt-input-field:focus {
            border-color: #673DE6;
            box-shadow: 0 0 0 3px rgba(103, 61, 230, 0.15);
        }

        /* GATEWAY SELECTOR */
        .vt-gateway-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 22px;
        }
        .vt-gateway-card {
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 16px;
            cursor: pointer;
            background: #ffffff;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .vt-gateway-card:hover {
            border-color: #673DE6;
            background: #faf8ff;
        }
        .vt-gateway-active {
            border-color: #673DE6 !important;
            background: #FAF8FF !important;
            box-shadow: 0 2px 8px rgba(103, 61, 230, 0.12);
        }
        .vt-gateway-radio {
            accent-color: #673DE6;
            width: 16px;
            height: 16px;
        }
        .vt-gateway-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .vt-gateway-title {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }
        .vt-gateway-sub {
            font-size: 11px;
            color: #64748b;
        }

        /* PRIMARY ACTION BUTTON */
        .vt-btn-deposit {
            background: #673DE6;
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            padding: 14px 24px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            box-shadow: 0 4px 14px rgba(103, 61, 230, 0.32);
            transition: all 0.2s ease;
        }
        .vt-btn-deposit:hover {
            background: #5428D8;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(103, 61, 230, 0.42);
        }
        .vt-btn-deposit:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
        }
        .vt-loading-state {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        @keyframes vtSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .vt-spinner {
            animation: vtSpin 0.75s linear infinite;
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        /* BENEFITS LIST */
        .vt-benefits-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .vt-benefit-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
        }
        .vt-benefit-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(103, 61, 230, 0.1);
            color: #673DE6;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
        }
        .vt-benefit-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .vt-benefit-title {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }
        .vt-benefit-desc {
            font-size: 12px;
            color: #64748b;
            line-height: 1.45;
        }

        /* 3. TRANSACTION HISTORY TABLE */
        .vt-table-wrap {
            overflow-x: auto;
        }
        .vt-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            text-align: left;
        }
        .vt-table th {
            background: #f8fafc;
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e2e8f0;
        }
        .vt-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }
        .vt-badge-paid {
            background: #d1fae5;
            color: #047857;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 9999px;
            display: inline-block;
        }
        .vt-badge-unpaid {
            background: #fef3c7;
            color: #b45309;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 9999px;
            display: inline-block;
        }
        .vt-btn-pay-link {
            background: #673DE6;
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: background 0.15s ease;
        }
        .vt-btn-pay-link:hover {
            background: #5428D8;
            color: #ffffff;
        }
        .vt-btn-view-link {
            color: #673DE6;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .vt-btn-view-link:hover {
            text-decoration: underline;
        }
    </style>

    <div class="vt-credit-wrap">
        <!-- 1. HERO BALANCE CARD -->
        <div class="vt-balance-hero">
            <div class="vt-balance-glow"></div>
            
            <div class="vt-balance-top">
                <div class="vt-balance-label">
                    <span>💳 Available Account Balance</span>
                </div>
                <span class="vt-balance-badge">
                    <span>●</span>
                    <span>Auto-Applied to Renewals</span>
                </span>
            </div>

            <div class="vt-balance-amount">
                <span>${{ number_format($balance, 2) }}</span>
                <span class="vt-balance-currency">USD</span>
            </div>

            <p class="vt-balance-desc">
                Your available account credit is automatically applied to any upcoming cloud VPS billing renewals and unpaid invoices, ensuring 100% server uptime without payment delays.
            </p>

            <div class="vt-balance-stats">
                <div class="vt-stat-item">
                    <span class="vt-stat-label">Total Deposits</span>
                    <span class="vt-stat-val">${{ number_format($paidDeposits->sum('total'), 2) }}</span>
                </div>
                <div class="vt-stat-item">
                    <span class="vt-stat-label">Deposit Invoices</span>
                    <span class="vt-stat-val">{{ $invoices->count() }} records</span>
                </div>
                <div class="vt-stat-item">
                    <span class="vt-stat-label">Pending Top-Ups</span>
                    <span class="vt-stat-val">{{ $unpaidDeposits->count() }} invoice(s)</span>
                </div>
                <div class="vt-stat-item">
                    <span class="vt-stat-label">Protection Status</span>
                    <span class="vt-stat-val" style="color: #34d399;">Active & Ready</span>
                </div>
            </div>
        </div>

        <!-- 2. TWO-COLUMN INTERACTIVE STAGE -->
        <div class="vt-credit-grid">
            <!-- Left: Add Funds Form -->
            <div class="vt-card">
                <div class="vt-card-head">
                    <h2 class="vt-card-title">
                        <span>⚡ Add Funds to Account</span>
                    </h2>
                    <p class="vt-card-subtitle">
                        Select a quick deposit preset or enter a custom amount to pre-fund your balance.
                    </p>
                </div>

                <!-- Quick Presets -->
                <div class="vt-presets-grid">
                    @foreach([10.00, 25.00, 50.00, 100.00, 250.00, 500.00] as $preset)
                        <button 
                            type="button" 
                            wire:click="selectPreset({{ $preset }})"
                            class="vt-preset-btn {{ (float)$depositAmount === (float)$preset ? 'vt-preset-active' : '' }}"
                        >
                            ${{ number_format($preset, 0) }}
                        </button>
                    @endforeach
                </div>

                <!-- Custom Amount Input -->
                <div class="vt-input-group">
                    <label class="vt-input-label">Deposit Amount (USD)</label>
                    <div class="vt-input-wrapper">
                        <span class="vt-input-prefix">$</span>
                        <input 
                            type="number" 
                            step="1" 
                            min="5" 
                            max="2500" 
                            wire:model.live="depositAmount"
                            class="vt-input-field"
                            placeholder="50.00"
                        />
                    </div>
                    <span style="font-size: 11px; color: #64748b; margin-top: 4px; display: block;">
                        Minimum deposit: $5.00 · Maximum per transaction: $2,500.00
                    </span>
                </div>

                <!-- Gateway Selection (Cryptocurrency Only) -->
                <div class="vt-input-group">
                    <label class="vt-input-label">Payment Gateway</label>
                    <div class="vt-gateway-card vt-gateway-active" style="cursor: default; width: 100%;">
                        <span style="font-size: 24px;">🪙</span>
                        <div class="vt-gateway-info" style="flex: 1;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <span class="vt-gateway-title">Cryptocurrency Payment</span>
                                <span style="font-size: 10px; font-weight: 700; background: #d1fae5; color: #047857; padding: 2px 8px; border-radius: 9999px;">
                                    Active & Instant
                                </span>
                            </div>
                            <span class="vt-gateway-sub">USDT (TRC20 / Polygon) & USDC (Polygon) · Instant Blockchain Verification</span>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button 
                    type="button" 
                    wire:click="createDeposit" 
                    wire:loading.attr="disabled"
                    class="vt-btn-deposit"
                >
                    <span wire:loading.remove wire:target="createDeposit" style="display: inline-flex; align-items: center; gap: 8px;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>Deposit ${{ number_format($depositAmount, 2) }} via Crypto</span>
                    </span>
                    <span wire:loading.inline-flex wire:target="createDeposit" class="vt-loading-state">
                        <svg class="vt-spinner" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity: 0.25;"></circle>
                            <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" style="opacity: 0.85;"></path>
                        </svg>
                        <span>Generating Invoice & Redirecting...</span>
                    </span>
                </button>
            </div>

            <!-- Right: Why Use Account Credit -->
            <div class="vt-card">
                <div class="vt-card-head">
                    <h2 class="vt-card-title">
                        <span>🛡️ Credit System Benefits</span>
                    </h2>
                    <p class="vt-card-subtitle">
                        Why high-performance developers keep a positive credit balance.
                    </p>
                </div>

                <div class="vt-benefits-list">
                    <div class="vt-benefit-item">
                        <div class="vt-benefit-icon">🛡️</div>
                        <div class="vt-benefit-text">
                            <span class="vt-benefit-title">Zero-Downtime Auto Renewal</span>
                            <span class="vt-benefit-desc">
                                When a cloud VPS cycle renews, your credit balance immediately pays the invoice, preventing server suspensions.
                            </span>
                        </div>
                    </div>

                    <div class="vt-benefit-item">
                        <div class="vt-benefit-icon">⚡</div>
                        <div class="vt-benefit-text">
                            <span class="vt-benefit-title">Instant Server Deployment</span>
                            <span class="vt-benefit-desc">
                                Deploy new high-memory NVMe nodes in seconds without entering card numbers or waiting on blockchain confirmations.
                            </span>
                        </div>
                    </div>

                    <div class="vt-benefit-item">
                        <div class="vt-benefit-icon">🔒</div>
                        <div class="vt-benefit-text">
                            <span class="vt-benefit-title">Non-Expiring Funds</span>
                            <span class="vt-benefit-desc">
                                All deposited account credits remain in your secure wallet permanently until utilized. No hidden dormancy fees.
                            </span>
                        </div>
                    </div>

                    <div class="vt-benefit-item">
                        <div class="vt-benefit-icon">🧾</div>
                        <div class="vt-benefit-text">
                            <span class="vt-benefit-title">Consolidated VAT & Tax Receipts</span>
                            <span class="vt-benefit-desc">
                                Make a single monthly deposit and download a single unified tax invoice for company bookkeeping.
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. DEPOSIT HISTORY & RECEIPTS -->
        <div class="vt-card">
            <div class="vt-card-head">
                <h2 class="vt-card-title">
                    <span>📑 Deposit History & Receipts</span>
                </h2>
                <p class="vt-card-subtitle">
                    All top-up deposits and credit invoices generated for your account.
                </p>
            </div>

            <div class="vt-table-wrap">
                <table class="vt-table">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Gateway</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $inv)
                            <tr>
                                <td>
                                    <strong style="color: #0f172a;">{{ $inv->invoice_number }}</strong>
                                </td>
                                <td>
                                    {{ $inv->created_at->format('M d, Y · H:i') }}
                                </td>
                                <td>
                                    <span style="font-weight: 600; text-transform: uppercase; font-size: 11px; color: #475569;">
                                        {{ $inv->payment_method ?? 'CRYPTO' }}
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: #0f172a;">${{ number_format($inv->total, 2) }}</strong>
                                </td>
                                <td>
                                    @if($inv->status === 'paid')
                                        <span class="vt-badge-paid">✓ Paid & Credited</span>
                                    @else
                                        <span class="vt-badge-unpaid">⏳ Awaiting Payment</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    @if($inv->status === 'unpaid')
                                        <a href="{{ route('checkout.crypto-pay', $inv->id) }}" class="vt-btn-pay-link">
                                            <span>Pay Now</span>
                                            <span>→</span>
                                        </a>
                                    @else
                                        <a href="{{ route('customer.invoices.print', $inv->id) }}" target="_blank" class="vt-btn-view-link">
                                            <span>Receipt PDF</span>
                                            <span>↗</span>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 36px; color: #64748b;">
                                    <div style="font-size: 24px; margin-bottom: 6px;">💳</div>
                                    <p style="font-size: 13px; margin: 0; font-weight: 600;">No deposit history yet.</p>
                                    <p style="font-size: 12px; margin: 4px 0 0 0; color: #94a3b8;">Use the deposit form above to add your first account funds.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
