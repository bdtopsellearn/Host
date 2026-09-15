<x-filament-widgets::widget class="fi-customer-stats-widget">
    <style>
        .fi-customer-stats-widget {
            grid-column: 1 / -1 !important;
            width: 100% !important;
        }

        .customer-bento-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.875rem;
            width: 100%;
            box-sizing: border-box;
        }

        @media (min-width: 1024px) {
            .customer-bento-grid {
                grid-template-columns: 1.25fr 1fr;
                align-items: stretch;
            }
        }

        /* Primary Card (Left 1x1 Compact Hero) */
        .customer-card-primary {
            background: #ffffff;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            transition: all 0.2s ease;
            box-sizing: border-box;
            min-height: 148px;
        }

        .customer-card-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2.5px;
            background: #673DE6;
        }

        .customer-card-primary:hover {
            border-color: #CBD5E1;
            box-shadow: 0 4px 14px rgba(103, 61, 230, 0.07);
            transform: translateY(-1px);
        }

        /* Right Column (2 Compact Stacked Cards) */
        .customer-secondary-column {
            display: grid;
            grid-template-rows: 1fr 1fr;
            gap: 0.625rem;
        }

        .customer-card-secondary {
            background: #ffffff;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            transition: all 0.2s ease;
            text-decoration: none !important;
            color: inherit !important;
            box-sizing: border-box;
            min-height: 68px;
        }

        .customer-card-secondary:hover {
            border-color: #CBD5E1;
            box-shadow: 0 3px 10px rgba(103, 61, 230, 0.06);
            transform: translateY(-1px);
        }

        /* Accent Elements (#673DE6) */
        .compact-icon-box {
            width: 26px;
            height: 26px;
            border-radius: 7px;
            background: rgba(103, 61, 230, 0.08);
            color: #673DE6;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .compact-accent-link {
            color: #673DE6;
            font-weight: 600;
            font-size: 0.75rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 2px;
            transition: all 0.15s ease;
        }

        .compact-accent-link:hover {
            color: #5428D8;
            text-decoration: underline;
        }

        .primary-mini-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #673DE6;
            color: #ffffff !important;
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.3rem 0.75rem;
            border-radius: 8px;
            text-decoration: none !important;
            transition: all 0.15s ease;
        }

        .primary-mini-btn:hover {
            background: #5428D8;
        }
    </style>

    <div class="customer-bento-grid">
        <!-- PRIMARY CARD: Compact Cloud Servers Hero (Left) -->
        <div class="customer-card-primary">
            <div>
                <!-- Header Line -->
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <span style="font-size: 0.8125rem; font-weight: 700; color: #0F172A; letter-spacing: -0.01em;">
                            Cloud VPS Instances
                        </span>
                        <span style="font-size: 0.625rem; font-weight: 700; text-transform: uppercase; color: #673DE6; background: rgba(103, 61, 230, 0.08); padding: 1px 6px; border-radius: 4px;">
                            Primary
                        </span>
                    </div>
                    <div class="compact-icon-box">
                        <svg style="width: 15px; height: 15px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 1 0 0 6h13.5a3 3 0 1 0 0-6m-16.5-3a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3m-19.5 0a4.5 4.5 0 0 1 .9-2.7L5.75 5.1a1.5 1.5 0 0 1 1.2-.6h10.1a1.5 1.5 0 0 1 1.2.6l2.1 3.45a4.5 4.5 0 0 1 .9 2.7m-16.5 0h16.5" />
                        </svg>
                    </div>
                </div>

                <!-- Number Display -->
                <div style="display: flex; align-items: baseline; gap: 6px; margin: 0.5rem 0 0.2rem 0;">
                    <span style="font-size: 1.75rem; font-weight: 800; color: #0F172A; line-height: 1.1; letter-spacing: -0.02em; font-variant-numeric: tabular-nums;">
                        {{ number_format($activeServicesCount) }}
                    </span>
                    <span style="font-size: 0.8125rem; font-weight: 500; color: #64748B;">
                        / {{ number_format($totalServicesCount) }} Active
                    </span>
                </div>

                <p style="font-size: 0.75rem; color: #64748B; margin: 0;">
                    {{ $activeServicesCount > 0 ? 'High-performance NVMe KVM hypervisors running.' : 'No active servers deployed yet.' }}
                </p>
            </div>

            <!-- Footer Action Line -->
            <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #F1F5F9; padding-top: 0.5rem; margin-top: 0.5rem;">
                <span style="font-size: 0.6875rem; color: #94A3B8;">
                    99.99% Hardware Uptime SLA
                </span>
                @if($activeServicesCount > 0)
                    <a href="{{ url('/customer/services') }}" class="primary-mini-btn">
                        Manage Servers &rarr;
                    </a>
                @else
                    <a href="{{ url('/plans') }}" target="_blank" class="primary-mini-btn">
                        Deploy Server &rarr;
                    </a>
                @endif
            </div>
        </div>

        <!-- SECONDARY COLUMN: 2 Compact Stacked Cards (Right) -->
        <div class="customer-secondary-column">
            <!-- 1. Secondary: Account Balance -->
            <div class="customer-card-secondary">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #64748B;">
                        Account Balance
                    </span>
                    <div class="compact-icon-box">
                        <svg style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 9m18 0V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v3" />
                        </svg>
                    </div>
                </div>

                <div style="display: flex; align-items: baseline; justify-content: space-between; margin-top: 0.25rem;">
                    <div style="display: flex; align-items: baseline; gap: 6px;">
                        <span style="font-size: 1.25rem; font-weight: 800; color: #0F172A; line-height: 1; letter-spacing: -0.02em; font-variant-numeric: tabular-nums;">
                            ${{ number_format($creditBalance, 2) }}
                        </span>
                        <span style="font-size: 0.6875rem; color: {{ $unpaidCount > 0 ? '#673DE6' : '#94A3B8' }}; font-weight: {{ $unpaidCount > 0 ? '600' : '400' }};">
                            {{ $unpaidCount > 0 ? "({$unpaidCount} unpaid)" : '· All settled' }}
                        </span>
                    </div>
                    <a href="{{ url('/customer/credit') }}" class="compact-accent-link">
                        + Add Funds &rarr;
                    </a>
                </div>
            </div>

            <!-- 2. Secondary: Support & Tickets -->
            <div class="customer-card-secondary">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #64748B;">
                        Support & Inquiries
                    </span>
                    <div class="compact-icon-box">
                        <svg style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                        </svg>
                    </div>
                </div>

                <div style="display: flex; align-items: baseline; justify-content: space-between; margin-top: 0.25rem;">
                    <div style="display: flex; align-items: baseline; gap: 6px;">
                        <span style="font-size: 1.25rem; font-weight: 800; color: #0F172A; line-height: 1; letter-spacing: -0.02em; font-variant-numeric: tabular-nums;">
                            {{ number_format($activeTicketsCount) }}
                        </span>
                        <span style="font-size: 0.6875rem; color: #94A3B8;">
                            {{ $activeTicketsCount > 0 ? 'Active inquiry' : 'Open tickets' }}
                        </span>
                    </div>
                    <a href="{{ url('/customer/support-tickets') }}" class="compact-accent-link">
                        Open Ticket &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
