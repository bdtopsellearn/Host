@props([
    'packages' => null,
    'id' => 'pricing-matrix-' . uniqid(),
    'showSwitcher' => true,
    'defaultCycle' => '24months',
    'limit' => null,
])

@php
    if (is_null($packages)) {
        try {
            $packages = \App\Models\Package::getCachedActivePackages();
        } catch (\Throwable $e) {
            $packages = collect();
        }
    }
    if (!is_null($limit) && $packages instanceof \Illuminate\Support\Collection) {
        $packages = $packages->take((int)$limit);
    }
@endphp

<div id="{{ $id }}" class="w-full pricing-matrix-container">
    @if($showSwitcher)
        <!-- Interactive 3-Option Billing Cycle Switcher (1 Month, 12 Months, 24 Months) -->
        <div class="flex justify-center mb-8 sm:mb-10">
            <div class="inline-flex items-center p-1.5 rounded-2xl bg-slate-200/80 border border-slate-300/80 shadow-inner flex-wrap justify-center gap-1">
                <button 
                    type="button" 
                    data-cycle="1month"
                    onclick="setMatrixBillingCycle('{{ $id }}', '1month')"
                    class="billing-toggle-btn px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 text-slate-700 hover:text-slate-900 flex items-center gap-1.5"
                >
                    1 Month
                </button>
                <button 
                    type="button" 
                    data-cycle="12months"
                    onclick="setMatrixBillingCycle('{{ $id }}', '12months')"
                    class="billing-toggle-btn px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 text-slate-700 hover:text-slate-900 flex items-center gap-1.5"
                >
                    <span>12 Months</span>
                    <span class="bg-purple-100 text-[#673DE6] text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full">Save 15%</span>
                </button>
                <button 
                    type="button" 
                    data-cycle="24months"
                    onclick="setMatrixBillingCycle('{{ $id }}', '24months')"
                    class="billing-toggle-btn px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 bg-[#673DE6] text-white shadow-md shadow-[#673DE6]/25 flex items-center gap-1.5"
                >
                    <span>24 Months</span>
                    <span class="bg-emerald-400 text-slate-950 text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full">Save 20%</span>
                </button>
            </div>
        </div>
    @endif

    @if($packages->count() > 0)
        <!-- Dynamic VPS Plans Grid (with top breathing room so elevated badges never clip) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch pt-6 sm:pt-8">
            @foreach($packages as $package)
                @php 
                    // Highlighting the 3rd plan (or 2nd if fewer) as Most Popular & Colorful
                    $isPopular = ($packages->count() >= 3) ? ($loop->iteration == 3) : ($loop->iteration == 2 || $loop->count == 1);
                    $delayClass = 'delay-' . ($loop->iteration * 100);
                @endphp

                <x-pricing-card 
                    :package="$package" 
                    :isPopular="$isPopular" 
                    :delayClass="$delayClass" 
                    badgeText="Most Popular"
                />
            @endforeach
        </div>
    @else
        <div class="bg-white border border-slate-200 rounded-3xl p-12 text-center max-w-xl mx-auto shadow-soft-sm">
            <p class="text-slate-600 font-medium">No active VPS packages found in the database.</p>
        </div>
    @endif
</div>

@once
<script>
    function setMatrixBillingCycle(containerId, cycle) {
        const container = document.getElementById(containerId) || document;
        const buttons = container.querySelectorAll('.billing-toggle-btn');
        const priceDisplays = container.querySelectorAll('.plan-price-display');
        const badgeDisplays = container.querySelectorAll('.billing-badge-display');

        const inactiveClass = 'billing-toggle-btn px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 text-slate-700 hover:text-slate-900 flex items-center gap-1.5';
        const activeClass = 'billing-toggle-btn px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 bg-[#673DE6] text-white shadow-md shadow-[#673DE6]/25 flex items-center gap-1.5';

        buttons.forEach(btn => {
            if (btn.getAttribute('data-cycle') === cycle) {
                btn.className = activeClass;
            } else {
                btn.className = inactiveClass;
            }
        });

        if (cycle === '1month' || cycle === 'monthly') {
            priceDisplays.forEach(el => {
                el.textContent = '$' + el.getAttribute('data-1month');
            });
            badgeDisplays.forEach(el => {
                el.textContent = 'Billed monthly (standard rate)';
                const isDark = el.closest('[style*="1E003E"]') || el.closest('.bg-\\[\\#16002C\\]');
                el.className = isDark ? 'mt-2 text-xs font-medium text-purple-300 billing-badge-display' : 'mt-2 text-xs font-medium text-slate-500 billing-badge-display';
            });
        } else if (cycle === '12months' || cycle === 'annual' || cycle === 'annually') {
            priceDisplays.forEach(el => {
                el.textContent = '$' + el.getAttribute('data-12months');
            });
            badgeDisplays.forEach(el => {
                el.textContent = 'Renews every 12 months (15% off applied)';
                const isDark = el.closest('[style*="1E003E"]') || el.closest('.bg-\\[\\#16002C\\]');
                el.className = isDark ? 'mt-2 text-xs font-medium text-purple-200 billing-badge-display' : 'mt-2 text-xs font-medium text-purple-700 billing-badge-display';
            });
        } else {
            priceDisplays.forEach(el => {
                el.textContent = '$' + el.getAttribute('data-24months');
            });
            badgeDisplays.forEach(el => {
                el.textContent = 'Renews every 24 months (20% off applied)';
                const isDark = el.closest('[style*="1E003E"]') || el.closest('.bg-\\[\\#16002C\\]');
                el.className = isDark ? 'mt-2 text-xs font-medium text-emerald-400 billing-badge-display' : 'mt-2 text-xs font-medium text-emerald-600 billing-badge-display';
            });
        }

        // Dynamically update checkout button URLs to pass the selected billing cycle
        let cycleParam = 'biennially';
        if (cycle === '1month' || cycle === 'monthly') {
            cycleParam = 'monthly';
        } else if (cycle === '12months' || cycle === 'annual' || cycle === 'annually') {
            cycleParam = 'annually';
        }

        const checkoutBtns = container.querySelectorAll('.plan-checkout-btn');
        checkoutBtns.forEach(btn => {
            const pkgId = btn.getAttribute('data-package-id');
            if (pkgId) {
                btn.href = '/checkout/' + pkgId + '?cycle=' + cycleParam;
            }
        });
    }
</script>
@endonce
