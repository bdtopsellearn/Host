<x-filament-widgets::widget class="fi-vortex-stats-widget">
    <style>
        .fi-vortex-stats-widget {
            grid-column: 1 / -1 !important;
            width: 100% !important;
        }

        .admin-bento-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.875rem;
            width: 100%;
            box-sizing: border-box;
        }

        @media (min-width: 640px) and (max-width: 1023px) {
            .admin-bento-grid {
                grid-template-columns: 1fr 1fr;
            }
            .admin-secondary-column {
                grid-column: 1 / -1;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 0.625rem;
            }
        }

        @media (min-width: 1024px) {
            .admin-bento-grid {
                grid-template-columns: 1.15fr 1.15fr 1fr;
                align-items: stretch;
            }
        }

        /* 2 Big Primary Cards (Left 1 & Left 2) */
        .admin-card-primary {
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

        .admin-card-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2.5px;
            background: #673DE6;
        }

        .admin-card-primary:hover {
            border-color: #CBD5E1;
            box-shadow: 0 4px 14px rgba(103, 61, 230, 0.07);
            transform: translateY(-1px);
        }

        /* 2 Small Stacked Cards (Right Column) */
        .admin-secondary-column {
            display: grid;
            grid-template-rows: 1fr 1fr;
            gap: 0.625rem;
        }

        .admin-card-secondary {
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

        .admin-card-secondary:hover {
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

        .admin-mini-btn {
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

        .admin-mini-btn:hover {
            background: #5428D8;
        }
    </style>

    <div class="admin-bento-grid">
        <!-- BIG CARD 1 (Left): Platform Settled Revenue -->
        <div class="admin-card-primary">
            <div>
                <!-- Header Line -->
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <span style="font-size: 0.8125rem; font-weight: 700; color: #0F172A; letter-spacing: -0.01em;">
                            Platform Revenue
                        </span>
                        <span style="font-size: 0.625rem; font-weight: 700; text-transform: uppercase; color: #673DE6; background: rgba(103, 61, 230, 0.08); padding: 1px 6px; border-radius: 4px;">
                            Settled
                        </span>
                    </div>
                    <div class="compact-icon-box">
                        <svg style="width: 15px; height: 15px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                </div>

                <!-- Number Display -->
                <div style="display: flex; align-items: baseline; gap: 6px; margin: 0.5rem 0 0.2rem 0;">
                    <span style="font-size: 1.75rem; font-weight: 800; color: #0F172A; line-height: 1.1; letter-spacing: -0.02em; font-variant-numeric: tabular-nums;">
                        ${{ number_format($totalRevenue, 2) }}
                    </span>
                    <span style="font-size: 0.8125rem; font-weight: 500; color: #64748B;">
                        USD
                    </span>
                </div>

                <p style="font-size: 0.75rem; color: #64748B; margin: 0;">
                    Verified ledger settlements from crypto gateway.
                </p>
            </div>

            <!-- Footer Action Line -->
            <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #F1F5F9; padding-top: 0.5rem; margin-top: 0.5rem;">
                <span style="font-size: 0.6875rem; color: #94A3B8;">
                    7-Day Velocity Active
                </span>
                <a href="{{ url('/admin/invoices') }}" class="compact-accent-link">
                    Invoices &rarr;
                </a>
            </div>
        </div>

        <!-- BIG CARD 2 (Left): Active Cloud VPS Fleet -->
        <div class="admin-card-primary">
            <div>
                <!-- Header Line -->
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <span style="font-size: 0.8125rem; font-weight: 700; color: #0F172A; letter-spacing: -0.01em;">
                            Cloud VPS Fleet
                        </span>
                        <span style="font-size: 0.625rem; font-weight: 700; text-transform: uppercase; color: #673DE6; background: rgba(103, 61, 230, 0.08); padding: 1px 6px; border-radius: 4px;">
                            Production
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
                    Enterprise NVMe KVM hypervisor instances online.
                </p>
            </div>

            <!-- Footer Action Line -->
            <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #F1F5F9; padding-top: 0.5rem; margin-top: 0.5rem;">
                <span style="font-size: 0.6875rem; color: #94A3B8;">
                    99.99% Hardware Availability
                </span>
                <a href="{{ url('/admin/packages') }}" class="compact-accent-link">
                    VPS Packages &rarr;
                </a>
            </div>
        </div>

        <!-- SECONDARY COLUMN: 2 Small Stacked Cards (Right) -->
        <div class="admin-secondary-column">
            <!-- 1. Small Card: Deployment Queue -->
            <div class="admin-card-secondary">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #64748B;">
                        Deployment Queue
                    </span>
                    <div class="compact-icon-box">
                        <svg style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                    </div>
                </div>

                <div style="display: flex; align-items: baseline; justify-content: space-between; margin-top: 0.25rem;">
                    <div style="display: flex; align-items: baseline; gap: 6px;">
                        <span style="font-size: 1.25rem; font-weight: 800; color: #0F172A; line-height: 1; letter-spacing: -0.02em; font-variant-numeric: tabular-nums;">
                            {{ $pendingOrdersCount }}
                        </span>
                        <span style="font-size: 0.6875rem; color: {{ $pendingOrdersCount > 0 ? '#673DE6' : '#94A3B8' }}; font-weight: {{ $pendingOrdersCount > 0 ? '600' : '400' }};">
                            {{ $pendingOrdersCount > 0 ? "({$pendingOrdersCount} pending)" : '· All Clear' }}
                        </span>
                    </div>
                    <a href="{{ url('/admin/orders') }}" class="compact-accent-link">
                        Orders &rarr;
                    </a>
                </div>
            </div>

            <!-- 2. Small Card: Clients & Support -->
            <div class="admin-card-secondary">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #64748B;">
                        Clients & Support
                    </span>
                    <div class="compact-icon-box">
                        <svg style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </div>
                </div>

                <div style="display: flex; align-items: baseline; justify-content: space-between; margin-top: 0.25rem;">
                    <div style="display: flex; align-items: baseline; gap: 6px;">
                        <span style="font-size: 1.25rem; font-weight: 800; color: #0F172A; line-height: 1; letter-spacing: -0.02em; font-variant-numeric: tabular-nums;">
                            {{ number_format($totalUsers) }}
                        </span>
                        <span style="font-size: 0.6875rem; color: {{ $openTicketsCount > 0 ? '#673DE6' : '#94A3B8' }}; font-weight: {{ $openTicketsCount > 0 ? '600' : '400' }};">
                            Users · {{ $openTicketsCount }} open ticket(s)
                        </span>
                    </div>
                    <a href="{{ url('/admin/support-tickets') }}" class="compact-accent-link">
                        Support &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
