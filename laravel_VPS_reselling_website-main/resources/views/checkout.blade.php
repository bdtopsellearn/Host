<x-app-layout 
    :title="'Configure & Checkout - ' . $package->name" 
    description="Customize your high-performance NVMe cloud VPS instance with 1-click OS install, global datacenter regions, and instant automated provisioning."
    headerVariant="minimal"
    robots="noindex, nofollow"
>
    @php
        $specs = is_string($package->specs) ? json_decode($package->specs, true) : (is_array($package->specs) ? $package->specs : []);
        
        $coresVal = !empty($specs['cores']) 
            ? (str_contains(strtolower((string)$specs['cores']), 'core') || str_contains(strtolower((string)$specs['cores']), 'vcpu') 
                ? $specs['cores'] 
                : $specs['cores'] . ' vCPU Cores') 
            : '1 vCPU Core';

        $ramVal = !empty($specs['memory']) 
            ? (str_contains(strtolower((string)$specs['memory']), 'ram') || str_contains(strtolower((string)$specs['memory']), 'ddr') 
                ? $specs['memory'] 
                : $specs['memory'] . ' DDR5 RAM') 
            : '2 GB DDR5 RAM';

        $rawStorage = !empty($specs['storage']) ? (string)$specs['storage'] : '40 GB';
        $cleanStorage = preg_replace('/(nvme|ssd|storage)/i', '', $rawStorage);
        $storageVal = trim($cleanStorage) . ' Gen4 NVMe';

        $portVal = !empty($specs['port']) 
            ? $specs['port'] 
            : (!empty($specs['bandwidth']) ? $specs['bandwidth'] : '1 Gbps Unmetered');

        $baseMonthly = (float) $package->price_monthly;
        $annualMonthly = !empty($package->price_annually) ? round(((float)$package->price_annually)/12, 2) : round($baseMonthly * 0.85, 2); // 15% off
        if ($annualMonthly <= 0 && $baseMonthly > 0) $annualMonthly = round($baseMonthly * 0.85, 2);

        $biennialMonthly = !empty($package->price_biennially) ? round(((float)$package->price_biennially)/24, 2) : round($baseMonthly * 0.80, 2); // 20% off
        if ($biennialMonthly <= 0 && $baseMonthly > 0) $biennialMonthly = round($baseMonthly * 0.80, 2);

        // Pre-selected cycle passed from Homepage / Plans page or default to 24 months
        $currentCycle = request('cycle', request('billing_cycle', $selectedCycle ?? old('billing_cycle', 'biennially')));
        if (in_array($currentCycle, ['1month', 'monthly', 'month'])) {
            $currentCycle = 'monthly';
        } elseif (in_array($currentCycle, ['12months', 'annual', 'annually', '1year', 'year'])) {
            $currentCycle = 'annually';
        } else {
            $currentCycle = 'biennially';
        }

        // Addons collections from controller or DB fallback via 2-Layer AddonResolverService
        $resolver = app(\App\Services\AddonResolverService::class);
        $resolvedAddons = isset($addons) && $addons->isNotEmpty() ? $addons : $resolver->getResolvedAddonsForPackage($package);
        $osAddons = $resolvedAddons->get('os', collect());
        $regionAddons = $resolvedAddons->get('region', collect());
        $storageAddons = $resolvedAddons->get('storage', collect());
        $backupAddon = $resolvedAddons->get('backup', collect())->firstWhere('value', '1');
        $networkAddon = $resolvedAddons->get('network', collect())->firstWhere('value', '1');

        // Organize OS Addons by Distribution Family for Contabo-style selection
        $osFamilyConfig = [
            'ubuntu' => [
                'id' => 'ubuntu',
                'name' => 'Ubuntu',
                'badge' => 'Enterprise Linux',
                'desc' => 'Ubuntu is a free, open-source Linux-based operating system. It is renowned for user-friendliness, stability, and extensive community support, widely deployed on servers, desktops, and enterprise cloud environments.',
            ],
            'debian' => [
                'id' => 'debian',
                'name' => 'Debian',
                'badge' => 'Rock-Solid Stability',
                'desc' => 'Debian is a versatile, community-driven universal operating system recognized for its rock-solid reliability, security adherence, and extensive package repository.',
            ],
            'rhel' => [
                'id' => 'rhel',
                'name' => 'RHEL Variants',
                'badge' => 'Enterprise Production',
                'desc' => 'Enterprise-grade Linux distributions (AlmaLinux & Rocky Linux) built 1:1 compatible with Red Hat Enterprise Linux sources, engineered for enterprise workloads and cPanel compatibility.',
            ],
            'windows' => [
                'id' => 'windows',
                'name' => 'Windows-Server',
                'badge' => 'Pre-activated with RDP',
                'desc' => 'Official Microsoft Windows Server Datacenter edition with pre-activated license, graphical desktop interface, and full Administrator Remote Desktop (RDP) access.',
            ],
        ];

        $osFamilies = [];
        foreach ($osFamilyConfig as $fKey => $fData) {
            $items = $osAddons->filter(fn($a) => $a->os_family === $fKey)->values();
            if ($items->isNotEmpty()) {
                $fData['items'] = $items;
                $osFamilies[$fKey] = $fData;
            }
        }

        $selectedOSVal = old('os', $osAddons->firstWhere('is_out_of_stock', false)?->value ?? $osAddons->first()?->value);
        $currentSelectedOS = $osAddons->firstWhere('value', $selectedOSVal) ?? $osAddons->first();
        $initialActiveFamily = $currentSelectedOS?->os_family ?? 'ubuntu';

        $osFamilyDataForJs = [];
        foreach ($osFamilies as $fKey => $fData) {
            $osFamilyDataForJs[$fKey] = [
                'id' => $fData['id'],
                'name' => $fData['name'],
                'desc' => $fData['desc'],
                'badge' => $fData['badge'],
                'items' => $fData['items']->map(function($item) {
                    return [
                        'value' => $item->value,
                        'name' => $item->name,
                        'price' => (float) $item->price,
                        'is_out_of_stock' => (bool) $item->is_out_of_stock,
                    ];
                })->values()->all(),
            ];
        }
    @endphp

    <div class="py-10 md:py-16 bg-slate-50/70 min-h-[90vh]">
        <div class="w-full max-w-[1680px] mx-auto px-4 sm:px-8 lg:px-12 xl:px-16">
            
            <!-- Top Funnel Header -->
            <div class="text-center max-w-2xl mx-auto mb-10">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-purple-50 border border-purple-200 text-[#673DE6] text-xs font-bold uppercase tracking-wider mb-3 shadow-soft-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>60-Second Instant Cloud Provisioning</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Configure Your VPS Server
                </h1>
                <p class="text-sm sm:text-base text-slate-600 mt-2">
                    Customize your cloud instance specifications. Your virtual server deploys automatically upon payment confirmation.
                </p>
            </div>

            <!-- Standard 3-Step Funnel Stepper Card -->
            <div class="max-w-3xl mx-auto mb-10">
                <div class="bg-white border border-slate-200/90 rounded-2xl p-2 sm:p-2.5 shadow-soft-sm">
                    <div class="grid grid-cols-3 gap-1.5 sm:gap-3">
                        
                        <!-- Step 1: Active -->
                        <div class="flex items-center gap-2 sm:gap-3 p-2 sm:p-2.5 rounded-xl bg-purple-50/90 border border-purple-200/80 text-[#673DE6]">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-[#673DE6] text-white font-extrabold text-xs sm:text-sm flex items-center justify-center shadow-md shadow-[#673DE6]/30 shrink-0">
                                1
                            </div>
                            <div class="min-w-0">
                                <span class="hidden sm:block text-[10px] font-extrabold uppercase tracking-wider text-purple-600 leading-none mb-0.5">Step 1</span>
                                <span class="block text-xs sm:text-sm font-bold text-slate-900 truncate">Configure VPS</span>
                            </div>
                        </div>

                        <!-- Step 2: Next -->
                        <div class="flex items-center gap-2 sm:gap-3 p-2 sm:p-2.5 rounded-xl bg-slate-50/70 border border-slate-100 text-slate-400">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white border border-slate-200 text-slate-500 font-bold text-xs sm:text-sm flex items-center justify-center shadow-soft-sm shrink-0">
                                2
                            </div>
                            <div class="min-w-0">
                                <span class="hidden sm:block text-[10px] font-bold uppercase tracking-wider text-slate-400 leading-none mb-0.5">Step 2</span>
                                <span class="block text-xs sm:text-sm font-semibold text-slate-500 truncate">Account Identity</span>
                            </div>
                        </div>

                        <!-- Step 3: Final -->
                        <div class="flex items-center gap-2 sm:gap-3 p-2 sm:p-2.5 rounded-xl bg-slate-50/70 border border-slate-100 text-slate-400">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white border border-slate-200 text-slate-500 font-bold text-xs sm:text-sm flex items-center justify-center shadow-soft-sm shrink-0">
                                3
                            </div>
                            <div class="min-w-0">
                                <span class="hidden sm:block text-[10px] font-bold uppercase tracking-wider text-slate-400 leading-none mb-0.5">Step 3</span>
                                <span class="block text-xs sm:text-sm font-semibold text-slate-500 truncate">Payment & Deploy</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Global Validation Alerts -->
            @if(isset($errors) && $errors->any())
                <div class="w-full mb-8 bg-rose-50 border border-rose-200 text-rose-800 p-5 rounded-2xl shadow-soft-sm">
                    <div class="flex items-center gap-2.5 font-bold text-sm mb-2 text-rose-900">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Please correct the following before proceeding:</span>
                    </div>
                    <ul class="list-disc pl-6 space-y-1 text-xs sm:text-sm text-rose-700 font-medium">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="checkout-form" action="{{ route('checkout.configure', $package->id) }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 xl:gap-12 items-start w-full">
                    
                    <!-- LEFT COLUMN: Progressive Order Funnel Steps (7-8 Cols) -->
                    <div class="lg:col-span-7 xl:col-span-8 space-y-8">
                        
                        <!-- STEP 1: CHOOSE BILLING PERIOD (Hostinger / SaaS Standard) -->
                        <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-soft-sm">
                            <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-full bg-[#673DE6] text-white font-bold text-xs flex items-center justify-center shadow-md shadow-[#673DE6]/30">1</span>
                                    <h2 class="text-lg font-bold text-slate-900">Choose Billing Period</h2>
                                </div>
                                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full">Save up to 20%</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                                
                                <!-- 1 Month -->
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="billing_cycle" value="monthly" class="peer sr-only" {{ $currentCycle === 'monthly' ? 'checked' : '' }} onchange="updateCalculations()">
                                    <div class="h-full p-4 rounded-2xl border-2 border-slate-200 bg-white hover:border-purple-300 peer-checked:border-[#673DE6] peer-checked:bg-purple-50/40 peer-checked:shadow-soft-md transition-all flex flex-col justify-between text-center group">
                                        <div class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">1 Month</div>
                                        <div class="my-2">
                                            <span class="text-2xl font-extrabold text-slate-900">${{ number_format($baseMonthly, 2) }}</span>
                                            <span class="text-xs text-slate-500 block">/month</span>
                                        </div>
                                        <div class="text-[11px] text-slate-500">Standard monthly rate</div>
                                    </div>
                                </label>

                                <!-- 12 Months (SAVE 15%) -->
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="billing_cycle" value="annually" class="peer sr-only" {{ $currentCycle === 'annually' ? 'checked' : '' }} onchange="updateCalculations()">
                                    <div class="h-full p-4 rounded-2xl border-2 border-slate-200 bg-white hover:border-purple-300 peer-checked:border-[#673DE6] peer-checked:bg-purple-50/40 peer-checked:shadow-soft-md transition-all flex flex-col justify-between text-center relative group">
                                        <!-- Top Badge -->
                                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-[#673DE6] text-white text-[10px] font-extrabold uppercase tracking-wider py-0.5 px-2.5 rounded-full shadow-sm whitespace-nowrap">
                                            Save 15%
                                        </div>
                                        <div class="text-xs font-bold text-purple-900 uppercase tracking-wider mb-1 pt-1">12 Months</div>
                                        <div class="my-2">
                                            <span class="text-2xl font-extrabold text-[#673DE6]">${{ number_format($annualMonthly, 2) }}</span>
                                            <span class="text-xs text-slate-500 block">/month</span>
                                        </div>
                                        <div class="text-[11px] font-semibold text-emerald-600">Billed annually</div>
                                    </div>
                                </label>

                                <!-- 24 Months (SAVE 20% - RECOMMENDED) -->
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="billing_cycle" value="biennially" class="peer sr-only" {{ $currentCycle === 'biennially' ? 'checked' : '' }} onchange="updateCalculations()">
                                    <div class="h-full p-4 rounded-2xl border-2 border-slate-200 bg-white hover:border-purple-300 peer-checked:border-[#673DE6] peer-checked:bg-purple-50/40 peer-checked:shadow-soft-md transition-all flex flex-col justify-between text-center relative group">
                                        <!-- Top Badge -->
                                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-emerald-600 text-white text-[10px] font-extrabold uppercase tracking-wider py-0.5 px-2.5 rounded-full shadow-sm whitespace-nowrap">
                                            Save 20% • Best Value
                                        </div>
                                        <div class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-1 pt-1">24 Months</div>
                                        <div class="my-2">
                                            <span class="text-2xl font-extrabold text-slate-900">${{ number_format($biennialMonthly, 2) }}</span>
                                            <span class="text-xs text-slate-500 block">/month</span>
                                        </div>
                                        <div class="text-[11px] font-semibold text-emerald-600">Billed 2 years</div>
                                    </div>
                                </label>

                            </div>
                        </div>

                        <!-- STEP 2: CLOUD SERVER CONFIGURATOR -->
                        <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-soft-sm space-y-6">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-full bg-[#673DE6] text-white font-bold text-xs flex items-center justify-center shadow-md shadow-[#673DE6]/30">2</span>
                                    <h2 class="text-lg font-bold text-slate-900">Server Configuration</h2>
                                </div>
                                <span class="text-xs text-slate-500 font-medium">1-Click Auto Deploy</span>
                            </div>

                            <!-- 1. Operating System Selector (Contabo-Style Family Configurator) -->
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                        Select Operating System
                                    </label>
                                    <span class="text-xs text-slate-500 font-medium flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Click family card to customize version
                                    </span>
                                </div>

                                <!-- Underlying hidden radios for exact OS version selection & form submission -->
                                <div class="sr-only" aria-hidden="true">
                                    @foreach($osAddons as $os)
                                        <input type="radio" 
                                               name="os" 
                                               id="os_radio_{{ $os->value }}" 
                                               value="{{ $os->value }}" 
                                               data-price="{{ $os->price }}" 
                                               data-name="{{ $os->name }}" 
                                               data-family="{{ $os->os_family }}" 
                                               class="addon-radio" 
                                               {{ $os->is_out_of_stock ? 'disabled' : '' }} 
                                               {{ $selectedOSVal === $os->value ? 'checked' : '' }}
                                               onchange="updateOSDisplay('{{ $os->name }}'); updateCalculations();">
                                    @endforeach
                                </div>

                                <!-- 4-Family OS Cards Grid -->
                                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-3" id="os-family-cards-grid">
                                    @foreach($osFamilies as $fKey => $family)
                                        @php
                                            $isFamilyActive = ($initialActiveFamily === $fKey);
                                            $familySelectedOS = $family['items']->firstWhere('value', $selectedOSVal) ?? $family['items']->first();
                                            $hasPrice = $familySelectedOS && $familySelectedOS->price > 0;
                                        @endphp
                                        <div id="os-family-card-{{ $fKey }}"
                                             onclick="handleFamilyCardClick('{{ $fKey }}')"
                                             class="os-family-card cursor-pointer p-3.5 sm:p-4 rounded-2xl border-2 transition-all duration-200 flex flex-col justify-between group relative select-none {{ $isFamilyActive ? 'border-[#673DE6] bg-purple-50/50 shadow-sm shadow-purple-600/10' : 'border-slate-200 bg-white hover:border-purple-300 hover:shadow-soft-sm' }}">
                                            
                                            <!-- Active Indicator Pin -->
                                            <div id="os-family-badge-active-{{ $fKey }}" class="absolute -top-3 left-1/2 -translate-x-1/2 {{ $isFamilyActive ? 'flex' : 'hidden' }} items-center gap-1 bg-[#673DE6] text-white text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full shadow-sm whitespace-nowrap z-10">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                <span>Selected</span>
                                            </div>

                                            <div>
                                                <!-- Card Top: Logo on Left, Price Tag on Right -->
                                                <div class="flex items-start justify-between gap-1.5 mb-2">
                                                    <div class="w-9 h-9 rounded-xl bg-slate-100/90 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                                        @if($fKey === 'ubuntu')
                                                            <!-- Ubuntu Logo SVG -->
                                                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                                                                <circle cx="12" cy="12" r="10" fill="#E95420"/>
                                                                <path d="M12 4.5a7.5 7.5 0 0 0-4.7 1.7l1.4 1.4A5.5 5.5 0 0 1 12 6.5a5.5 5.5 0 0 1 5.3 4H19.4A7.5 7.5 0 0 0 12 4.5zm-5.7 3.3a7.4 7.4 0 0 0-1.8 4.2h2a5.4 5.4 0 0 1 1.2-2.8L6.3 7.8zm11.4 4.2h-2a5.5 5.5 0 0 1-5.3 4 5.5 5.5 0 0 1-3.3-1.1l-1.4 1.4A7.5 7.5 0 0 0 12 19.5a7.5 7.5 0 0 0 7.4-7.5zM4.5 12a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm12.3-5.2a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm0 13.4a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" fill="#FFFFFF"/>
                                                            </svg>
                                                        @elseif($fKey === 'debian')
                                                            <!-- Debian Logo SVG -->
                                                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                                                                <circle cx="12" cy="12" r="10" fill="#D70A53"/>
                                                                <path d="M11.8 5.2c3.5 0 6.3 2.7 6.4 6.1.1 2.9-1.8 5.4-4.6 6-2.4.5-4.7-.9-5.4-3.2-.6-2 .4-4 2.4-4.8 1.6-.6 3.4.1 4 1.7.5 1.3-.1 2.7-1.4 3.2-1 .4-2.1-.1-2.5-1.1-.3-.7.1-1.6.8-1.9.5-.2 1.1 0 1.3.5.2.4 0 .9-.4 1.1-.3.1-.7 0-.8-.3" stroke="#FFFFFF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                        @elseif($fKey === 'rhel')
                                                            <!-- RHEL Variants (AlmaLinux / Rocky) Logo SVG -->
                                                            <svg class="w-6 h-6" viewBox="0 0 48 48" fill="none">
                                                                <path d="M24 4 L30 18 L24 24 L18 18 Z" fill="#9333EA"/>
                                                                <path d="M44 24 L30 30 L24 24 L30 18 Z" fill="#EAB308"/>
                                                                <path d="M24 44 L18 30 L24 24 L30 30 Z" fill="#10B981"/>
                                                                <path d="M4 24 L18 18 L24 24 L18 30 Z" fill="#0284C7"/>
                                                                <path d="M24 8 L27 15 L24 18 L21 15 Z" fill="#C084FC"/>
                                                                <path d="M40 24 L33 27 L30 24 L33 21 Z" fill="#FDE047"/>
                                                                <path d="M24 40 L21 33 L24 30 L27 33 Z" fill="#6EE7B7"/>
                                                                <path d="M8 24 L15 21 L18 24 L15 27 Z" fill="#38BDF8"/>
                                                            </svg>
                                                        @elseif($fKey === 'windows')
                                                            <!-- Windows Server Logo SVG -->
                                                            <svg class="w-6 h-6" viewBox="0 0 88 88" fill="none">
                                                                <path d="M0 12.4 L35.6 7.5 V42.1 H0 V12.4 Z" fill="#0078D7"/>
                                                                <path d="M39.8 6.9 L88 0 V42.1 H39.8 V6.9 Z" fill="#0078D7"/>
                                                                <path d="M0 45.9 H35.6 V80.5 L0 75.6 V45.9 Z" fill="#0078D7"/>
                                                                <path d="M39.8 45.9 H88 V88 L39.8 81.1 V45.9 Z" fill="#0078D7"/>
                                                            </svg>
                                                        @endif
                                                    </div>

                                                    <!-- Price Tag Badge -->
                                                    <span id="os-family-price-tag-{{ $fKey }}" class="text-[10px] font-extrabold px-2 py-0.5 rounded-md shrink-0 whitespace-nowrap {{ $hasPrice ? 'text-purple-700 bg-purple-100/70 border border-purple-200' : 'text-emerald-700 bg-emerald-50 border border-emerald-200' }}">
                                                        {{ $hasPrice ? '+$' . number_format($familySelectedOS->price, 2) . '/mo' : 'Free' }}
                                                    </span>
                                                </div>

                                                <!-- OS Family Name -->
                                                <div class="text-sm font-extrabold text-slate-900 tracking-tight leading-snug">
                                                    {{ $family['name'] }}
                                                </div>

                                                <!-- Version Subtitle -->
                                                <div class="mt-2">
                                                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Version:</div>
                                                    <div id="os-family-version-text-{{ $fKey }}" class="text-xs font-bold text-slate-900 truncate mt-0.5">
                                                        {{ $familySelectedOS?->name ?? 'Select Version' }}
                                                    </div>
                                                    <div class="text-[10px] text-slate-500 mt-0.5 truncate">{{ $family['badge'] }}</div>
                                                </div>
                                            </div>

                                            <!-- Bottom Change Edition Button Trigger -->
                                            <div class="mt-3.5 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                                                <span class="text-[11px] text-slate-400 font-medium">Edition</span>
                                                <span class="inline-flex items-center gap-1 font-bold text-[#673DE6] group-hover:text-[#5428D8] transition-colors">
                                                    <span>Change</span>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- 2. Datacenter Region Location Picker with Vector Flags -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">
                                    Select Datacenter Region
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @php
                                        $regionMeta = [
                                            'us_east' => ['city' => 'New York, US', 'latency' => '9 ms', 'flag' => 'us'],
                                            'us_west' => ['city' => 'Seattle, US', 'latency' => '11 ms', 'flag' => 'us'],
                                            'eu_central' => ['city' => 'Frankfurt, DE', 'latency' => '12 ms', 'flag' => 'de'],
                                            'uk_london' => ['city' => 'London, GB', 'latency' => '14 ms', 'flag' => 'uk'],
                                            'asia_singapore' => ['city' => 'APAC Hub, SG', 'latency' => '38 ms', 'flag' => 'sg'],
                                            'au_sydney' => ['city' => 'Sydney, AU', 'latency' => '42 ms', 'flag' => 'au'],
                                        ];
                                    @endphp

                                    @php $firstSelectableRegion = $regionAddons->firstWhere('is_out_of_stock', false)?->value; @endphp
                                    @foreach($regionAddons as $index => $region)
                                    @php
                                        $meta = $regionMeta[$region->value] ?? ['city' => 'Global DC', 'latency' => '15 ms', 'flag' => 'global'];
                                    @endphp
                                    <label class="{{ $region->is_out_of_stock ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'cursor-pointer' }}">
                                        <input type="radio" name="datacenter" value="{{ $region->value }}" data-price="{{ $region->price }}" class="addon-radio peer sr-only" {{ $region->is_out_of_stock ? 'disabled' : '' }} {{ old('datacenter', $firstSelectableRegion) === $region->value ? 'checked' : '' }} onchange="updateLocationDisplay('{{ $region->name }}'); updateCalculations();">
                                        <div class="p-3.5 rounded-2xl border-2 border-slate-200 peer-checked:border-[#673DE6] peer-checked:bg-purple-50/50 hover:border-purple-300 transition-all text-center flex flex-col items-center justify-between h-full group">
                                            <div class="w-10 h-7 rounded-md overflow-hidden shadow-sm border border-slate-200/80 mb-2 flex items-center justify-center bg-white shrink-0 group-hover:scale-105 transition-transform">
                                                @if($meta['flag'] === 'us')
                                                    <!-- USA Flag -->
                                                    <svg class="w-full h-full" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                                                        <rect width="640" height="480" fill="#b22234"/>
                                                        <path d="M0,37h640M0,111h640M0,185h640M0,258h640M0,332h640M0,406h640" stroke="#fff" stroke-width="37"/>
                                                        <rect width="260" height="259" fill="#3c3b6e"/>
                                                    </svg>
                                                @elseif($meta['flag'] === 'de')
                                                    <!-- Germany Flag -->
                                                    <svg class="w-full h-full" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                                                        <rect width="640" height="160" fill="#000000"/>
                                                        <rect y="160" width="640" height="160" fill="#dd0000"/>
                                                        <rect y="320" width="640" height="160" fill="#ffce00"/>
                                                    </svg>
                                                @elseif($meta['flag'] === 'uk')
                                                    <!-- UK Flag -->
                                                    <svg class="w-full h-full" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                                                        <rect width="640" height="480" fill="#012169"/>
                                                        <path d="M0,0 L640,480 M640,0 L0,480" stroke="#fff" stroke-width="60"/>
                                                        <path d="M0,0 L640,480 M640,0 L0,480" stroke="#c8102e" stroke-width="40"/>
                                                        <path d="M320,0 V480 M0,240 H640" stroke="#fff" stroke-width="100"/>
                                                        <path d="M320,0 V480 M0,240 H640" stroke="#c8102e" stroke-width="60"/>
                                                    </svg>
                                                @elseif($meta['flag'] === 'sg')
                                                    <!-- Singapore Flag -->
                                                    <svg class="w-full h-full" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                                                        <rect width="640" height="240" fill="#ed2939"/>
                                                        <rect y="240" width="640" height="240" fill="#ffffff"/>
                                                        <circle cx="120" cy="120" r="70" fill="#ffffff"/>
                                                        <circle cx="140" cy="120" r="70" fill="#ed2939"/>
                                                    </svg>
                                                @elseif($meta['flag'] === 'au')
                                                    <!-- Australia Flag -->
                                                    <svg class="w-full h-full" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                                                        <rect width="640" height="480" fill="#012169"/>
                                                        <rect width="320" height="240" fill="#012169"/>
                                                        <path d="M0,0 L320,240 M320,0 L0,240" stroke="#fff" stroke-width="30"/>
                                                        <path d="M0,0 L320,240 M320,0 L0,240" stroke="#c8102e" stroke-width="20"/>
                                                        <path d="M160,0 V240 M0,120 H320" stroke="#fff" stroke-width="50"/>
                                                        <path d="M160,0 V240 M0,120 H320" stroke="#c8102e" stroke-width="30"/>
                                                        <circle cx="480" cy="120" r="16" fill="#fff"/>
                                                        <circle cx="480" cy="340" r="16" fill="#fff"/>
                                                        <circle cx="400" cy="220" r="16" fill="#fff"/>
                                                        <circle cx="540" cy="240" r="16" fill="#fff"/>
                                                    </svg>
                                                @else
                                                    <span class="text-base">🌐</span>
                                                @endif
                                            </div>
                                            <div class="text-xs font-bold text-slate-900 leading-tight">{{ $region->name }}</div>
                                            <div class="text-[10px] text-slate-500 mb-1.5">{{ $meta['city'] }}</div>
                                            <div class="flex items-center gap-1.5">
                                                @if($region->is_out_of_stock)
                                                    <span class="text-[10px] text-rose-600 bg-rose-50 border border-rose-200 font-mono font-bold px-2 py-0.5 rounded-full">Sold Out</span>
                                                @else
                                                    <span class="text-[10px] text-emerald-600 bg-emerald-50 border border-emerald-200 font-mono font-bold px-2 py-0.5 rounded-full">{{ $meta['latency'] }}</span>
                                                    @if($region->price > 0)
                                                        <span class="text-[10px] text-purple-700 bg-purple-50 font-bold px-1.5 py-0.5 rounded">+${{ number_format($region->price, 2) }}</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- 3. NVMe Storage Capacity Upgrade -->
                            @if($storageAddons->isNotEmpty())
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">
                                    Primary NVMe Storage Tier
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    @php $firstSelectableStorage = $storageAddons->firstWhere('is_out_of_stock', false)?->value; @endphp
                                    @foreach($storageAddons as $index => $storage)
                                    <label class="{{ $storage->is_out_of_stock ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'cursor-pointer' }}">
                                        <input type="radio" name="storage_type" value="{{ $storage->value }}" data-price="{{ $storage->price }}" class="addon-radio peer sr-only" {{ $storage->is_out_of_stock ? 'disabled' : '' }} {{ old('storage_type', $firstSelectableStorage) === $storage->value ? 'checked' : '' }} onchange="updateStorageDisplay('{{ $storage->name }}'); updateCalculations();">
                                        <div class="p-3.5 rounded-2xl border-2 border-slate-200 peer-checked:border-[#673DE6] peer-checked:bg-purple-50/50 hover:border-purple-300 transition-all flex items-center justify-between">
                                            <div class="flex items-center gap-2.5">
                                                <span class="text-lg">💾</span>
                                                <div>
                                                    <div class="text-xs font-bold text-slate-900">{{ $storage->name }}</div>
                                                    <div class="text-[10px] text-slate-500">PCIe 4.0 Ultra-Fast</div>
                                                </div>
                                            </div>
                                            @if($storage->is_out_of_stock)
                                                <span class="text-xs font-bold text-rose-600">Sold Out</span>
                                            @else
                                                <span class="text-xs font-bold {{ $storage->price > 0 ? 'text-purple-700' : 'text-emerald-600' }}">
                                                    {{ $storage->price > 0 ? '+$' . number_format($storage->price, 2) . '/mo' : 'Included' }}
                                                </span>
                                            @endif
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- 4. Optional Enterprise Addons (Backups & Private Networking) -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">
                                    High-Availability Add-Ons
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    
                                    <!-- Automated Daily Backups -->
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="auto_backup" value="1" data-price="{{ $backupAddon ? $backupAddon->price : 1.95 }}" class="addon-checkbox peer sr-only" {{ old('auto_backup') ? 'checked' : '' }} onchange="toggleFeatureSummary('backup', this.checked); updateCalculations();">
                                        <div class="p-4 rounded-2xl border-2 border-slate-200 peer-checked:border-[#673DE6] peer-checked:bg-purple-50/40 hover:border-purple-300 transition-all flex items-start justify-between gap-3 h-full">
                                            <div class="flex items-start gap-3">
                                                <div class="w-8 h-8 rounded-xl bg-purple-50 text-[#673DE6] flex items-center justify-center text-sm shrink-0 mt-0.5">🛡️</div>
                                                <div>
                                                    <div class="text-xs font-bold text-slate-900">Automated Daily Backups</div>
                                                    <div class="text-[11px] text-slate-500 leading-snug mt-0.5">Automated off-site snapshot retention with 1-click restore.</div>
                                                </div>
                                            </div>
                                            <span class="text-xs font-bold text-[#673DE6] shrink-0">+${{ number_format($backupAddon ? $backupAddon->price : 1.95, 2) }}/mo</span>
                                        </div>
                                    </label>

                                    <!-- Private Networking (VPC) -->
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="private_networking" value="1" data-price="{{ $networkAddon ? $networkAddon->price : 2.75 }}" class="addon-checkbox peer sr-only" {{ old('private_networking') ? 'checked' : '' }} onchange="toggleFeatureSummary('network', this.checked); updateCalculations();">
                                        <div class="p-4 rounded-2xl border-2 border-slate-200 peer-checked:border-[#673DE6] peer-checked:bg-purple-50/40 hover:border-purple-300 transition-all flex items-start justify-between gap-3 h-full">
                                            <div class="flex items-start gap-3">
                                                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shrink-0 mt-0.5">🔒</div>
                                                <div>
                                                    <div class="text-xs font-bold text-slate-900">Private Networking (VPC)</div>
                                                    <div class="text-[11px] text-slate-500 leading-snug mt-0.5">Isolated 10 Gbps backend LAN interconnect across nodes.</div>
                                                </div>
                                            </div>
                                            <span class="text-xs font-bold text-[#673DE6] shrink-0">+${{ number_format($networkAddon ? $networkAddon->price : 2.75, 2) }}/mo</span>
                                        </div>
                                    </label>

                                </div>
                            </div>

                            <!-- 5. Server Hostname & Root Password (Optional Customization) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Server Hostname <span class="text-slate-400 font-normal">(Optional)</span></label>
                                    <input type="text" name="hostname" value="{{ old('hostname') }}" placeholder="vps-01.vortexcloud.net" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-[#673DE6] focus:border-[#673DE6] outline-none transition-all placeholder:text-slate-400 font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Root / SSH Password <span class="text-slate-400 font-normal">(Optional)</span></label>
                                    <input type="password" name="root_password" placeholder="Auto-generated if empty" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-[#673DE6] focus:border-[#673DE6] outline-none transition-all placeholder:text-slate-400">
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- RIGHT COLUMN: Real-Time Sticky Order Summary (4-5 Cols) -->
                    <div class="lg:col-span-5 xl:col-span-4 sticky top-28 space-y-5">
                        
                        <div class="rounded-3xl bg-white border border-slate-200 shadow-soft-lg overflow-hidden">
                            
                            <!-- Cosmic Violet Gradient Header Strip -->
                            <div class="p-6 bg-gradient-to-r from-[#180033] to-[#25004A] text-white">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold uppercase tracking-wider text-purple-300">Selected Plan</span>
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[10px] font-bold font-mono">Instant Setup</span>
                                </div>
                                <h3 class="text-2xl font-extrabold text-white tracking-tight">{{ $package->name }}</h3>
                                <div class="text-xs text-purple-200 mt-0.5">High-Performance KVM Cloud Instance</div>
                            </div>

                            <!-- Hardware Specs Snapshot -->
                            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                                <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Included Hardware Specs</div>
                                <ul class="space-y-2.5 text-xs sm:text-sm text-slate-700">
                                    <li class="flex items-center justify-between">
                                        <span class="text-slate-500 flex items-center gap-2">
                                            <span class="text-purple-600">⚡</span> CPU Compute
                                        </span>
                                        <span class="font-bold text-slate-900">{{ $coresVal }}</span>
                                    </li>
                                    <li class="flex items-center justify-between">
                                        <span class="text-slate-500 flex items-center gap-2">
                                            <span class="text-purple-600">🧠</span> Memory (RAM)
                                        </span>
                                        <span class="font-bold text-slate-900">{{ $ramVal }}</span>
                                    </li>
                                    <li class="flex items-center justify-between">
                                        <span class="text-slate-500 flex items-center gap-2">
                                            <span class="text-purple-600">💾</span> NVMe Storage
                                        </span>
                                        <span id="summary-storage-display" class="font-bold text-slate-900">{{ $storageVal }}</span>
                                    </li>
                                    <li class="flex items-center justify-between">
                                        <span class="text-slate-500 flex items-center gap-2">
                                            <span class="text-purple-600">🌐</span> Network Port
                                        </span>
                                        <span class="font-bold text-slate-900">{{ $portVal }}</span>
                                    </li>
                                    <li class="flex items-center justify-between">
                                        <span class="text-slate-500 flex items-center gap-2">
                                            <span class="text-purple-600">🐧</span> OS Image
                                        </span>
                                        <span id="summary-os-display" class="font-bold text-slate-900">
                                            {{ $currentSelectedOS?->name ?? ($osAddons->first()?->name ?? 'Ubuntu 24.04 LTS') }}
                                        </span>
                                    </li>
                                    <li class="flex items-center justify-between">
                                        <span class="text-slate-500 flex items-center gap-2">
                                            <span class="text-purple-600">📍</span> Datacenter
                                        </span>
                                        <span id="summary-loc-display" class="font-bold text-slate-900">
                                            {{ $regionAddons->first()?->name ?? 'US East (New York)' }}
                                        </span>
                                    </li>
                                    <li id="summary-backup-row" class="{{ old('auto_backup') ? 'flex' : 'hidden' }} items-center justify-between">
                                        <span class="text-slate-500 flex items-center gap-2">
                                            <span class="text-purple-600">🛡️</span> Auto Backups
                                        </span>
                                        <span class="font-bold text-emerald-600">Enabled</span>
                                    </li>
                                    <li id="summary-network-row" class="{{ old('private_networking') ? 'flex' : 'hidden' }} items-center justify-between">
                                        <span class="text-slate-500 flex items-center gap-2">
                                            <span class="text-purple-600">🔒</span> Private Network
                                        </span>
                                        <span class="font-bold text-emerald-600">Enabled</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Live Price Calculation Breakdown -->
                            <div class="p-6 space-y-3">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-slate-600">Period Subtotal</span>
                                    <span id="summary-subtotal" class="font-bold text-slate-900">${{ number_format($baseMonthly * 24, 2) }}</span>
                                </div>
                                <div id="summary-cycle-discount-row" class="flex items-center justify-between text-sm text-emerald-600 font-semibold">
                                    <span>Term Discount (<span id="summary-discount-pct">20%</span>)</span>
                                    <span id="summary-discount-amt">-${{ number_format(($baseMonthly * 24) * 0.20, 2) }}</span>
                                </div>
                                <div id="summary-coupon-row" class="hidden items-center justify-between text-sm text-purple-600 font-semibold">
                                    <span>Coupon Discount</span>
                                    <span id="summary-coupon-amt">-$0.00</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-slate-600">Setup & Provisioning</span>
                                    <span class="font-bold text-emerald-600 uppercase text-xs">FREE ($0.00)</span>
                                </div>
                                <div class="pt-3 border-t border-slate-100 flex items-baseline justify-between">
                                    <div>
                                        <div class="text-base font-bold text-slate-900">Total Due Today:</div>
                                        <div class="text-xs text-slate-500">Includes all taxes & fees</div>
                                    </div>
                                    <div class="text-right">
                                        <div id="summary-total-display" class="text-3xl font-extrabold text-slate-900 tracking-tight">
                                            ${{ number_format($biennialMonthly * 24, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Main CTA Submit Button -->
                            <div class="p-6 pt-0">
                                <button type="submit" id="submit-button" class="btn-shimmer w-full bg-[#673DE6] hover:bg-[#5428D8] text-white font-extrabold py-4 px-6 rounded-xl shadow-xl shadow-[#673DE6]/30 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2.5 text-base cursor-pointer">
                                    <span id="button-text">
                                        @auth
                                            Proceed to Payment & Deploy
                                        @else
                                            Continue to Account & Payment
                                        @endauth
                                    </span>
                                    <svg class="w-5 h-5 text-purple-200 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    <svg id="spinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </button>
                                <p class="text-[11px] text-center text-slate-400 mt-2.5">
                                    @guest
                                        Next step: Sign in or create an account to deploy your server.
                                    @else
                                        Next step: Finalize payment method and provision server.
                                    @endguest
                                </p>
                            </div>

                        </div>

                        <!-- Trust & Security Badges -->
                        <div class="p-5 rounded-2xl bg-white border border-slate-200 space-y-3 text-xs text-slate-600 shadow-soft-sm">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span><strong>99.99% Uptime SLA Guarantee</strong> enterprise hardware</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span><strong>256-Bit SSL Encryption</strong> powered by Stripe</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span><strong>24/7 Dedicated Sysadmin Support</strong> desk</span>
                            </div>
                        </div>

                    </div>

                </div>
            </form>

        </div>
    </div>

    <!-- CONTABO-STYLE OS SELECTION POPUP MODAL -->
    <div id="os-selection-modal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-950/60 backdrop-blur-sm hidden transition-all duration-200"
         onclick="closeOSModal()">
        
        <!-- Modal Card Container -->
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 w-full max-w-lg p-6 sm:p-8 relative transform transition-all select-none animate-fade-in-up"
             onclick="event.stopPropagation()">
            
            <!-- Header: Logo + Family Title + Close Button -->
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3.5">
                    <div id="modal-os-logo" class="w-10 h-10 rounded-xl bg-slate-100/90 flex items-center justify-center shrink-0">
                        <!-- Dynamic SVG injected via JS -->
                    </div>
                    <div>
                        <h3 id="modal-os-title" class="text-xl font-extrabold text-slate-900 tracking-tight">Ubuntu</h3>
                        <div id="modal-os-badge" class="text-[11px] font-semibold text-[#673DE6]">Enterprise Linux</div>
                    </div>
                </div>
                <button type="button" 
                        onclick="closeOSModal()" 
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Description Paragraph (matching Contabo screenshot) -->
            <p id="modal-os-desc" class="text-xs sm:text-sm text-slate-600 leading-relaxed mt-2 mb-6">
                Ubuntu is a free, open-source Linux-based operating system...
            </p>

            <!-- Version Selection Block -->
            <div class="relative">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Version</label>
                
                <!-- Custom Styled Select Trigger (Contabo style) -->
                <button type="button"
                        id="modal-dropdown-trigger"
                        onclick="toggleOSDropdown()"
                        class="w-full p-3.5 rounded-xl border-2 border-[#673DE6] bg-white flex items-center justify-between text-left cursor-pointer transition-all shadow-sm hover:border-[#5428D8] focus:outline-none">
                    <span id="modal-trigger-name" class="font-bold text-slate-900 text-xs sm:text-sm truncate">
                        Windows Server Datacenter 2025
                    </span>
                    <div class="flex items-center gap-2 shrink-0 ml-2">
                        <span id="modal-trigger-pricing" class="text-xs text-slate-600 font-medium">
                            17.00 $ / Month | 0.00 $ Setup fee
                        </span>
                        <svg id="modal-trigger-arrow" class="w-4 h-4 text-slate-700 transition-transform transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </button>

                <!-- Dropdown Popover List (Options beneath trigger) -->
                <div id="modal-dropdown-menu" 
                     class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-xl shadow-2xl z-30 max-h-60 overflow-y-auto divide-y divide-slate-100">
                    <!-- Populated dynamically per family -->
                </div>
            </div>

            <!-- Modal Footer Actions -->
            <div class="mt-8 pt-5 border-t border-slate-100 flex items-center justify-between gap-3">
                <span class="text-xs text-slate-500 font-medium flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>1-Click automated provisioning</span>
                </span>
                <button type="button" 
                        onclick="confirmAndCloseOSModal()" 
                        class="btn-shimmer px-6 py-2.5 rounded-xl bg-[#673DE6] hover:bg-[#5428D8] text-white text-xs sm:text-sm font-bold shadow-md shadow-purple-600/20 transition-all">
                    Select & Apply
                </button>
            </div>

        </div>
    </div>

    <!-- Stripe JS SDK & Real-Time Price/Auth Script -->
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const baseMonthly = {{ $baseMonthly }};
        let currentDiscountPercent = 20; // default for 24 mo
        let currentMonths = 24;

        // Contabo OS Modal & Family Logic
        const osFamilyData = @json($osFamilyDataForJs);
        let activeFamily = '{{ $initialActiveFamily }}';
        const selectedOSByFamily = {};

        Object.keys(osFamilyData).forEach(fKey => {
            const fam = osFamilyData[fKey];
            const currentMatch = fam.items.find(i => i.value === '{{ $selectedOSVal }}');
            selectedOSByFamily[fKey] = currentMatch ? currentMatch.value : (fam.items[0] ? fam.items[0].value : '');
        });

        const osFamilySVGs = {
            ubuntu: `<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="#E95420"/><path d="M12 4.5a7.5 7.5 0 0 0-4.7 1.7l1.4 1.4A5.5 5.5 0 0 1 12 6.5a5.5 5.5 0 0 1 5.3 4H19.4A7.5 7.5 0 0 0 12 4.5zm-5.7 3.3a7.4 7.4 0 0 0-1.8 4.2h2a5.4 5.4 0 0 1 1.2-2.8L6.3 7.8zm11.4 4.2h-2a5.5 5.5 0 0 1-5.3 4 5.5 5.5 0 0 1-3.3-1.1l-1.4 1.4A7.5 7.5 0 0 0 12 19.5a7.5 7.5 0 0 0 7.4-7.5zM4.5 12a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm12.3-5.2a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm0 13.4a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" fill="#FFFFFF"/></svg>`,
            debian: `<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="#D70A53"/><path d="M11.8 5.2c3.5 0 6.3 2.7 6.4 6.1.1 2.9-1.8 5.4-4.6 6-2.4.5-4.7-.9-5.4-3.2-.6-2 .4-4 2.4-4.8 1.6-.6 3.4.1 4 1.7.5 1.3-.1 2.7-1.4 3.2-1 .4-2.1-.1-2.5-1.1-.3-.7.1-1.6.8-1.9.5-.2 1.1 0 1.3.5.2.4 0 .9-.4 1.1-.3.1-.7 0-.8-.3" stroke="#FFFFFF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
            rhel: `<svg class="w-6 h-6" viewBox="0 0 48 48" fill="none"><path d="M24 4 L30 18 L24 24 L18 18 Z" fill="#9333EA"/><path d="M44 24 L30 30 L24 24 L30 18 Z" fill="#EAB308"/><path d="M24 44 L18 30 L24 24 L30 30 Z" fill="#10B981"/><path d="M4 24 L18 18 L24 24 L18 30 Z" fill="#0284C7"/><path d="M24 8 L27 15 L24 18 L21 15 Z" fill="#C084FC"/><path d="M40 24 L33 27 L30 24 L33 21 Z" fill="#FDE047"/><path d="M24 40 L21 33 L24 30 L27 33 Z" fill="#6EE7B7"/><path d="M8 24 L15 21 L18 24 L15 27 Z" fill="#38BDF8"/></svg>`,
            windows: `<svg class="w-6 h-6" viewBox="0 0 88 88" fill="none"><path d="M0 12.4 L35.6 7.5 V42.1 H0 V12.4 Z" fill="#0078D7"/><path d="M39.8 6.9 L88 0 V42.1 H39.8 V6.9 Z" fill="#0078D7"/><path d="M0 45.9 H35.6 V80.5 L0 75.6 V45.9 Z" fill="#0078D7"/><path d="M39.8 45.9 H88 V88 L39.8 81.1 V45.9 Z" fill="#0078D7"/></svg>`
        };

        function handleFamilyCardClick(familyKey) {
            const currentVal = selectedOSByFamily[familyKey];
            if (currentVal) {
                selectOSVersion(familyKey, currentVal);
            }
            openOSModal(familyKey);
        }

        function openOSModal(familyKey) {
            activeFamily = familyKey;
            const fam = osFamilyData[familyKey];
            if (!fam) return;

            document.getElementById('modal-os-title').textContent = fam.name;
            document.getElementById('modal-os-badge').textContent = fam.badge;
            document.getElementById('modal-os-desc').textContent = fam.desc;
            document.getElementById('modal-os-logo').innerHTML = osFamilySVGs[familyKey] || '';

            const currentVal = selectedOSByFamily[familyKey];
            const currentItem = fam.items.find(i => i.value === currentVal) || fam.items[0];
            if (currentItem) {
                updateModalTriggerDisplay(familyKey, currentItem);
            }

            renderDropdownItems(familyKey);
            closeOSDropdown();

            const modal = document.getElementById('os-selection-modal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeOSModal() {
            const modal = document.getElementById('os-selection-modal');
            if (modal) modal.classList.add('hidden');
            document.body.style.overflow = '';
            closeOSDropdown();
        }

        function confirmAndCloseOSModal() {
            closeOSModal();
        }

        function toggleOSDropdown() {
            const menu = document.getElementById('modal-dropdown-menu');
            const arrow = document.getElementById('modal-trigger-arrow');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                arrow.classList.add('rotate-180');
            } else {
                menu.classList.add('hidden');
                arrow.classList.remove('rotate-180');
            }
        }

        function closeOSDropdown() {
            const menu = document.getElementById('modal-dropdown-menu');
            const arrow = document.getElementById('modal-trigger-arrow');
            if (menu) menu.classList.add('hidden');
            if (arrow) arrow.classList.remove('rotate-180');
        }

        function updateModalTriggerDisplay(familyKey, item) {
            const nameEl = document.getElementById('modal-trigger-name');
            const pricingEl = document.getElementById('modal-trigger-pricing');
            if (nameEl) nameEl.textContent = item.name;
            if (pricingEl) {
                if (item.price > 0) {
                    pricingEl.textContent = '$' + item.price.toFixed(2) + ' / Month | $0.00 Setup fee';
                } else {
                    pricingEl.textContent = '$0.00 Setup fee';
                }
            }
        }

        function renderDropdownItems(familyKey) {
            const menu = document.getElementById('modal-dropdown-menu');
            if (!menu) return;
            const items = osFamilyData[familyKey]?.items || [];
            const currentVal = selectedOSByFamily[familyKey];

            let html = '';
            items.forEach(item => {
                const isSelected = (item.value === currentVal);
                const nameClass = isSelected ? 'text-[#673DE6] font-extrabold' : 'text-slate-700 font-semibold group-hover:text-slate-900';
                const priceStr = item.price > 0 ? ('$' + Number(item.price).toFixed(2)) : 'Free';

                html += `
                    <div onclick="selectOSVersion('${familyKey}', '${item.value}')"
                         class="px-4 py-3 cursor-pointer hover:bg-slate-50 flex items-center justify-between transition-colors group select-none ${isSelected ? 'bg-purple-50/50' : ''}">
                        <div class="flex items-center gap-2">
                            ${isSelected ? '<span class="w-1.5 h-1.5 rounded-full bg-[#673DE6]"></span>' : ''}
                            <span class="${nameClass} text-xs sm:text-sm">
                                ${item.name}
                            </span>
                        </div>
                        <span class="text-xs sm:text-sm font-bold ${item.price > 0 ? 'text-slate-900' : 'text-emerald-600'} shrink-0 ml-3">
                            ${priceStr}
                        </span>
                    </div>
                `;
            });
            menu.innerHTML = html;
        }

        function selectOSVersion(familyKey, osValue) {
            selectedOSByFamily[familyKey] = osValue;
            activeFamily = familyKey;

            const fam = osFamilyData[familyKey];
            if (!fam) return;
            const item = fam.items.find(i => i.value === osValue);
            if (!item) return;

            // Check hidden radio
            const radio = document.getElementById('os_radio_' + osValue);
            if (radio) {
                radio.checked = true;
            }

            // Update order summary OS text
            updateOSDisplay(item.name);

            // Update version text and price badge on the family card
            const cardVersionText = document.getElementById('os-family-version-text-' + familyKey);
            if (cardVersionText) cardVersionText.textContent = item.name;

            const cardPriceTag = document.getElementById('os-family-price-tag-' + familyKey);
            if (cardPriceTag) {
                if (item.price > 0) {
                    cardPriceTag.textContent = '+$' + Number(item.price).toFixed(2) + '/mo';
                    cardPriceTag.className = 'text-[10px] font-extrabold px-2 py-0.5 rounded-md shrink-0 whitespace-nowrap text-purple-700 bg-purple-100/70 border border-purple-200';
                } else {
                    cardPriceTag.textContent = 'Free';
                    cardPriceTag.className = 'text-[10px] font-extrabold px-2 py-0.5 rounded-md shrink-0 whitespace-nowrap text-emerald-700 bg-emerald-50 border border-emerald-200';
                }
            }

            // Update active family card styling
            Object.keys(osFamilyData).forEach(fKey => {
                const card = document.getElementById('os-family-card-' + fKey);
                const badge = document.getElementById('os-family-badge-active-' + fKey);
                if (fKey === familyKey) {
                    if (card) {
                        card.className = 'os-family-card cursor-pointer p-3.5 sm:p-4 rounded-2xl border-2 transition-all duration-200 flex flex-col justify-between group relative select-none border-[#673DE6] bg-purple-50/50 shadow-sm shadow-purple-600/10';
                    }
                    if (badge) {
                        badge.classList.remove('hidden');
                        badge.classList.add('flex');
                    }
                } else {
                    if (card) {
                        card.className = 'os-family-card cursor-pointer p-3.5 sm:p-4 rounded-2xl border-2 transition-all duration-200 flex flex-col justify-between group relative select-none border-slate-200 bg-white hover:border-purple-300 hover:shadow-soft-sm';
                    }
                    if (badge) {
                        badge.classList.remove('flex');
                        badge.classList.add('hidden');
                    }
                }
            });

            // Update modal trigger display & dropdown list
            updateModalTriggerDisplay(familyKey, item);
            renderDropdownItems(familyKey);
            closeOSDropdown();

            updateCalculations();
        }

        function updateOSDisplay(name) {
            const el = document.getElementById('summary-os-display');
            if (el) el.textContent = name;
        }

        function updateLocationDisplay(name) {
            const el = document.getElementById('summary-loc-display');
            if (el) el.textContent = name;
        }

        function updateStorageDisplay(name) {
            const el = document.getElementById('summary-storage-display');
            if (el) el.textContent = name;
        }

        function toggleFeatureSummary(feature, isChecked) {
            const row = document.getElementById('summary-' + feature + '-row');
            if (row) {
                if (isChecked) {
                    row.classList.remove('hidden');
                    row.classList.add('flex');
                } else {
                    row.classList.add('hidden');
                    row.classList.remove('flex');
                }
            }
        }

        function updateCalculations() {
            const selectedCycle = document.querySelector('input[name="billing_cycle"]:checked')?.value || 'biennially';
            
            if (selectedCycle === 'monthly' || selectedCycle === '1month') {
                currentMonths = 1;
                currentDiscountPercent = 0;
            } else if (selectedCycle === 'annually' || selectedCycle === '12months') {
                currentMonths = 12;
                currentDiscountPercent = 15;
            } else {
                currentMonths = 24;
                currentDiscountPercent = 20;
            }

            let addonsMonthly = 0;
            document.querySelectorAll('.addon-radio:checked').forEach(el => addonsMonthly += parseFloat(el.getAttribute('data-price') || 0));
            document.querySelectorAll('.addon-checkbox:checked').forEach(el => addonsMonthly += parseFloat(el.getAttribute('data-price') || 0));

            const rawBase = (baseMonthly + addonsMonthly) * currentMonths;
            const discountAmt = (rawBase * currentDiscountPercent) / 100;
            const total = rawBase - discountAmt;

            const subtotalEl = document.getElementById('summary-subtotal');
            if (subtotalEl) subtotalEl.textContent = '$' + rawBase.toFixed(2);
            
            const discRow = document.getElementById('summary-cycle-discount-row');
            if (discRow) {
                if (currentDiscountPercent > 0) {
                    discRow.classList.remove('hidden');
                    discRow.classList.add('flex');
                    const pctEl = document.getElementById('summary-discount-pct');
                    if (pctEl) pctEl.textContent = currentDiscountPercent + '%';
                    const amtEl = document.getElementById('summary-discount-amt');
                    if (amtEl) amtEl.textContent = '-$' + discountAmt.toFixed(2);
                } else {
                    discRow.classList.add('hidden');
                    discRow.classList.remove('flex');
                }
            }

            const totalEl = document.getElementById('summary-total-display');
            if (totalEl) totalEl.textContent = '$' + total.toFixed(2);
        }

        document.addEventListener("DOMContentLoaded", function() {
            updateCalculations();

            const form = document.getElementById('checkout-form');
            form.addEventListener('submit', function() {
                const btn = document.getElementById('submit-button');
                btn.disabled = true;
                document.getElementById('button-text').textContent = 'Saving Configuration...';
                document.getElementById('spinner').classList.remove('hidden');
            });
        });
    </script>
</x-app-layout>
