@props([
    'package',
    'isPopular' => false,
    'badgeText' => 'Most Popular',
    'delayClass' => 'delay-100',
    'billingCycle' => 'annually',
])

@php
    // Safe string sanitation
    $cleanName = trim($package->name ?? 'Cloud VPS');
    
    // Specs decoding
    $specs = is_string($package->specs) ? json_decode($package->specs, true) : (is_array($package->specs) ? $package->specs : []);
    
    // Price calculations: 1 Month, 12 Months (Save 15%), 24 Months (Save 20%)
    $price1Month = (float) ($package->price_monthly ?? 0);
    
    $price12Months = !empty($package->price_annually) ? round(((float) $package->price_annually) / 12, 2) : round($price1Month * 0.85, 2);
    if ($price12Months <= 0 && $price1Month > 0) {
        $price12Months = round($price1Month * 0.85, 2);
    }

    $price24Months = !empty($package->price_biennially) ? round(((float) $package->price_biennially) / 24, 2) : round($price1Month * 0.80, 2);
    if ($price24Months <= 0 && $price1Month > 0) {
        $price24Months = round($price1Month * 0.80, 2);
    }

    // Default spec fallbacks if not populated
    $coresText = !empty($specs['cores']) ? $specs['cores'] : '4 vCPU Cores';
    $ramText = !empty($specs['memory']) ? $specs['memory'] : '8 GB RAM';
    $storageText = !empty($specs['storage']) ? $specs['storage'] : '100 GB NVMe SSD';
    $bandwidthText = !empty($specs['bandwidth']) ? $specs['bandwidth'] : '1 Gbps Port / Unmetered';
    $snapshotsText = !empty($specs['snapshots']) ? $specs['snapshots'] : '1 Automated Snapshot';
    $category = strtolower($package->category ?? 'vps');

    $isRdp = $category === 'rdp' || str_contains(strtolower($cleanName), 'rdp') || str_contains(strtolower($package->description ?? ''), 'windows');
@endphp

@if($isPopular)
    <!-- ==========================================
         FEATURED TIER: COLORFUL COSMIC VIOLET CARD 
         (NO outer overflow-hidden so top badge never clips)
         ========================================== -->
    <div class="reveal-init {{ $delayClass }} card-interactive relative rounded-3xl p-7 sm:p-8 bg-[#16002C] border-2 border-[#673DE6] shadow-2xl shadow-purple-950/60 ring-2 ring-purple-500/30 flex flex-col justify-between md:-translate-y-3 transition-all duration-300 text-white group hover:shadow-purple-900/80 z-10" style="background: linear-gradient(180deg, #1E003E 0%, #14002B 50%, #25004A 100%);">
        
        <!-- Internal Ambient Glow (Restricted with overflow-hidden ONLY here) -->
        <div class="absolute inset-0 overflow-hidden rounded-3xl pointer-events-none">
            <div class="absolute -top-12 -right-12 w-44 h-44 bg-purple-500/25 rounded-full blur-2xl animate-pulse-glow"></div>
            <div class="absolute -bottom-12 -left-12 w-44 h-44 bg-fuchsia-600/15 rounded-full blur-2xl"></div>
        </div>

        <!-- Floating Most Popular Pill Badge (Fully visible, z-30, unclipped) -->
        <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-[#673DE6] text-white text-[11px] font-extrabold uppercase tracking-wider py-1 px-4 rounded-full shadow-lg shadow-purple-950/80 border border-purple-300/40 flex items-center gap-1.5 whitespace-nowrap z-30 pointer-events-none">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
            <span>{{ $badgeText }}</span>
        </div>

        <div class="relative z-10">
            <!-- Header: Title & Category Pill -->
            <div class="mb-6 pt-2">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-2xl font-extrabold text-white tracking-tight">{{ $cleanName }}</h3>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-white/10 border border-white/20 text-purple-200 uppercase">
                        {{ $isRdp ? 'RDP / Windows' : 'KVM Cloud' }}
                    </span>
                </div>
                <p class="text-xs text-purple-200 min-h-[36px] leading-relaxed font-normal">
                    {{ $package->description ?? 'Optimized for high-traffic production APIs, databases, and microservices.' }}
                </p>
            </div>

            <!-- Price Display with Dynamic Data Attributes for JS Toggle (1 Month / 12 Months / 24 Months) -->
            <div class="pb-6 mb-6 border-b border-white/10">
                <div class="flex items-baseline gap-1.5">
                    <span 
                        class="text-4xl sm:text-5xl font-extrabold text-white tracking-tight plan-price-display"
                        data-1month="{{ number_format($price1Month, 2) }}"
                        data-12months="{{ number_format($price12Months, 2) }}"
                        data-24months="{{ number_format($price24Months, 2) }}"
                        data-monthly="{{ number_format($price1Month, 2) }}"
                        data-annual="{{ number_format($price12Months, 2) }}"
                    >
                        ${{ number_format($price24Months, 2) }}
                    </span>
                    <span class="text-sm font-medium text-purple-300">/mo</span>
                </div>
                <div class="mt-2 text-xs font-medium text-emerald-400 billing-badge-display">
                    Renews every 24 months (20% off applied)
                </div>
            </div>

            <!-- Hardware Specs Micro-List -->
            <div class="space-y-3.5 mb-8">
                <div class="text-xs font-bold uppercase tracking-wider text-purple-300">Included Hardware Specs</div>
                <ul class="space-y-3 text-sm text-slate-100 font-medium">
                    <li class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-lg bg-white/10 text-emerald-400 flex items-center justify-center shrink-0 border border-white/15">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span><strong>{{ $coresText }}</strong></span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-lg bg-white/10 text-emerald-400 flex items-center justify-center shrink-0 border border-white/15">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span><strong>{{ $ramText }}</strong></span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-lg bg-white/10 text-emerald-400 flex items-center justify-center shrink-0 border border-white/15">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span><strong>{{ $storageText }}</strong></span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-lg bg-white/10 text-emerald-400 flex items-center justify-center shrink-0 border border-white/15">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span><strong>{{ $bandwidthText }}</strong></span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-lg bg-white/10 text-emerald-400 flex items-center justify-center shrink-0 border border-white/15">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>1 Dedicated IPv4 + /64 IPv6</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-lg bg-white/10 text-emerald-400 flex items-center justify-center shrink-0 border border-white/15">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>Full Root & VNC Console</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- CTA Action Trigger -->
        <div class="relative z-10">
            <a 
                href="{{ !empty($package->id) ? route('checkout.show', ['package' => $package->id, 'cycle' => 'biennially']) : url('/plans') }}" 
                data-package-id="{{ $package->id ?? '' }}"
                class="plan-checkout-btn btn-shimmer block w-full text-center bg-[#673DE6] hover:bg-[#5428D8] text-white py-3.5 rounded-xl font-bold transition-all duration-200 shadow-xl shadow-[#673DE6]/40 hover:scale-[1.02] active:scale-[0.98]"
            >
                Choose {{ $cleanName }}
            </a>
            <p class="text-[11px] text-center text-purple-300 mt-2.5">
                Instant automated setup in &lt; 60s
            </p>
        </div>

    </div>
@else
    <!-- ==========================================
         STANDARD CLEAN SAAS WHITE CARD
         ========================================== -->
    <div class="reveal-init {{ $delayClass }} card-interactive relative bg-white rounded-3xl p-7 sm:p-8 flex flex-col justify-between transition-all duration-300 border border-slate-200 shadow-soft-sm hover:shadow-soft-md hover:border-purple-200">
        <div>
            <!-- Header: Title & Category Pill -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $cleanName }}</h3>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 uppercase">
                        {{ $isRdp ? 'RDP' : 'KVM' }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 min-h-[36px] leading-relaxed">
                    {{ $package->description ?? 'Ideal for staging environments, personal blogs, and developer tooling.' }}
                </p>
            </div>

            <!-- Price Display with Dynamic Data Attributes for JS Toggle -->
            <div class="pb-6 mb-6 border-b border-slate-100">
                <div class="flex items-baseline gap-1.5">
                    <span 
                        class="text-4xl sm:text-5xl font-extrabold text-slate-900 tracking-tight plan-price-display"
                        data-1month="{{ number_format($price1Month, 2) }}"
                        data-12months="{{ number_format($price12Months, 2) }}"
                        data-24months="{{ number_format($price24Months, 2) }}"
                        data-monthly="{{ number_format($price1Month, 2) }}"
                        data-annual="{{ number_format($price12Months, 2) }}"
                    >
                        ${{ number_format($price24Months, 2) }}
                    </span>
                    <span class="text-sm font-medium text-slate-500">/mo</span>
                </div>
                <div class="mt-2 text-xs font-medium text-emerald-600 billing-badge-display">
                    Renews every 24 months (20% off applied)
                </div>
            </div>

            <!-- Hardware Specs Micro-List -->
            <div class="space-y-3.5 mb-8">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Included Hardware Specs</div>
                <ul class="space-y-3 text-sm text-slate-700 font-medium">
                    <li class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-lg bg-purple-50 text-[#673DE6] flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span><strong>{{ $coresText }}</strong></span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-lg bg-purple-50 text-[#673DE6] flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span><strong>{{ $ramText }}</strong></span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-lg bg-purple-50 text-[#673DE6] flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span><strong>{{ $storageText }}</strong></span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-lg bg-purple-50 text-[#673DE6] flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span><strong>{{ $bandwidthText }}</strong></span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-lg bg-purple-50 text-[#673DE6] flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>1 Dedicated IPv4 + /64 IPv6</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-lg bg-purple-50 text-[#673DE6] flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>Full Root & VNC Console</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- CTA Action Trigger -->
        <div>
            <a 
                href="{{ !empty($package->id) ? route('checkout.show', ['package' => $package->id, 'cycle' => 'biennially']) : url('/plans') }}" 
                data-package-id="{{ $package->id ?? '' }}"
                class="plan-checkout-btn btn-shimmer block w-full text-center bg-white hover:bg-slate-50 text-slate-800 hover:text-slate-900 border border-slate-200 hover:border-slate-300 shadow-soft-sm py-3.5 rounded-xl font-bold transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]"
            >
                Choose {{ $cleanName }}
            </a>
            <p class="text-[11px] text-center text-slate-400 mt-2.5">
                Instant automated setup in &lt; 60s
            </p>
        </div>
    </div>
@endif
