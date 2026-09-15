<x-app-layout 
    title="Secure Payment & Server Deployment - VortexCloud" 
    description="Finalize your cloud VPS order and initiate immediate server provisioning." 
    headerVariant="solid"
    robots="noindex, nofollow">

    @php
        $user = auth()->user();
        $coresVal = $specsJson['cores'] ?? '4 vCPU Cores';
        $ramVal = $specsJson['memory'] ?? '8 GB RAM';
        $storageVal = $pendingOrder['storage_type'] ?? ($specsJson['storage'] ?? '100 GB Gen4 NVMe');
        $portVal = $specsJson['bandwidth'] ?? ($specsJson['port'] ?? '1 Gbps');

        $stripeActive = $stripeEnabled ?? config('services.stripe.enabled', false);
        $cryptoActive = $cryptoEnabled ?? config('services.crypto.enabled', true);

        $gateways = $gateways ?? \App\Models\PaymentSetting::getActiveGateways();
        $bkash = $gateways['bkash'] ?? null;
        $nagad = $gateways['nagad'] ?? null;
        $rocket = $gateways['rocket'] ?? null;
        $binance = $gateways['binance'] ?? null;

        $rateBdt = $bkash->conversion_rate_bdt_usd ?? 125.00;
        $bdtAmount = round($finalTotal * $rateBdt);
        $bdtFormatted = number_format($bdtAmount, 0);

        $supportPhone = $bkash->support_phone ?? ($nagad->support_phone ?? '01619789895');
        $supportWhatsapp = $bkash->support_whatsapp ?? ($nagad->support_whatsapp ?? '01619789895');

        $defaultPaymentMethod = old('payment_type', 'bkash');
    @endphp

    <div class="py-10 md:py-16 bg-slate-50/70 min-h-[90vh]">
        <div class="w-full max-w-[1680px] mx-auto px-4 sm:px-8 lg:px-12 xl:px-16">
            
            <!-- Top Funnel Header -->
            <div class="text-center max-w-2xl mx-auto mb-10">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-purple-50 border border-purple-200 text-[#673DE6] text-xs font-bold uppercase tracking-wider mb-3 shadow-soft-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Step 3 of 3: Final Step</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Review Order & Secure Payment
                </h1>
                <p class="text-sm sm:text-base text-slate-600 mt-2">
                    Review your configured server specs and select your preferred payment gateway to initiate instant provisioning.
                </p>
            </div>

            <!-- Standard 3-Step Funnel Stepper Card -->
            <div class="max-w-3xl mx-auto mb-10">
                <div class="bg-white border border-slate-200/90 rounded-2xl p-2 sm:p-2.5 shadow-soft-sm">
                    <div class="grid grid-cols-3 gap-1.5 sm:gap-3">
                        
                        <!-- Step 1: Completed -->
                        <a href="{{ route('checkout.show', ['package' => $package->id, 'cycle' => $cycle]) }}" class="flex items-center gap-2 sm:gap-3 p-2 sm:p-2.5 rounded-xl bg-emerald-50/80 border border-emerald-200/80 text-emerald-700 hover:bg-emerald-100/70 transition-all group" title="Click to edit server configuration">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-emerald-500 text-white font-black text-xs sm:text-sm flex items-center justify-center shadow-md shadow-emerald-500/25 shrink-0 group-hover:scale-105 transition-transform">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="min-w-0">
                                <span class="hidden sm:block text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 leading-none mb-0.5">Completed</span>
                                <span class="block text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-700 truncate">1. Configure</span>
                            </div>
                        </a>

                        <!-- Step 2: Completed -->
                        <div class="flex items-center gap-2 sm:gap-3 p-2 sm:p-2.5 rounded-xl bg-emerald-50/80 border border-emerald-200/80 text-emerald-700">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-emerald-500 text-white font-black text-xs sm:text-sm flex items-center justify-center shadow-md shadow-emerald-500/25 shrink-0">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="min-w-0">
                                <span class="hidden sm:block text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 leading-none mb-0.5">Verified</span>
                                <span class="block text-xs sm:text-sm font-bold text-slate-900 truncate">2. Identity</span>
                            </div>
                        </div>

                        <!-- Step 3: Active -->
                        <div class="flex items-center gap-2 sm:gap-3 p-2 sm:p-2.5 rounded-xl bg-purple-50/90 border border-purple-200/80 text-[#673DE6]">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-[#673DE6] text-white font-extrabold text-xs sm:text-sm flex items-center justify-center shadow-md shadow-[#673DE6]/30 shrink-0">
                                3
                            </div>
                            <div class="min-w-0">
                                <span class="hidden sm:block text-[10px] font-extrabold uppercase tracking-wider text-purple-600 leading-none mb-0.5">Active Step</span>
                                <span class="block text-xs sm:text-sm font-bold text-slate-900 truncate">3. Payment & Deploy</span>
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

            <form id="checkout-payment-form" action="{{ route('checkout.process', $package->id) }}" method="POST">
                @csrf
                <input type="hidden" name="payment_method_id" id="payment_method_id">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 xl:gap-12 items-start w-full">
                    
                    <!-- LEFT COLUMN: Account & Payment Steps (7-8 Cols) -->
                    <div class="lg:col-span-7 xl:col-span-8 space-y-8">
                        
                        <!-- CARD 0: OFFICIAL INVOICE GENERATED -->
                        @if(isset($invoice) && $invoice)
                            <div class="bg-white p-6 sm:p-7 rounded-3xl border-2 border-purple-200 shadow-soft-sm relative overflow-hidden">
                                <div class="absolute top-0 right-0 transform translate-x-4 -translate-y-4 w-28 h-28 bg-purple-500/10 rounded-full blur-2xl pointer-events-none"></div>
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 mb-4 border-b border-slate-100">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-purple-100 text-[#673DE6] flex items-center justify-center font-bold shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h2 class="text-lg font-extrabold text-slate-900">Invoice #{{ $invoice->invoice_number }}</h2>
                                                <span class="px-2.5 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-[11px] font-bold uppercase tracking-wider flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                    Pending Payment
                                                </span>
                                            </div>
                                            <p class="text-xs text-slate-500 mt-0.5">Order Ref: <span class="font-mono font-semibold text-slate-700">{{ $order->order_number ?? 'ORD-PENDING' }}</span> • Generated on {{ $invoice->created_at ? $invoice->created_at->format('M d, Y') : now()->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ url('/customer/invoices') }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#673DE6] hover:text-[#5428D8] bg-purple-50 hover:bg-purple-100 border border-purple-200 px-3.5 py-2 rounded-xl transition-all self-start sm:self-auto shrink-0">
                                        <span>View In Portal</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                        <span class="text-slate-400 block font-medium">Billed To</span>
                                        <span class="font-bold text-slate-900 truncate block">{{ $user->name }}</span>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                        <span class="text-slate-400 block font-medium">Due Date</span>
                                        <span class="font-bold text-slate-900 block">{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') : now()->addDays(7)->format('M d, Y') }}</span>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                        <span class="text-slate-400 block font-medium">Amount Due</span>
                                        <span class="font-bold text-[#673DE6] text-sm block">${{ number_format($invoice->total, 2) }}</span>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                        <span class="text-slate-400 block font-medium">Payment Status</span>
                                        <span class="font-bold text-amber-600 block">Awaiting Payment</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- CARD 1: VERIFIED CUSTOMER IDENTITY -->
                        <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-soft-sm">
                            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-full bg-emerald-500 text-white font-bold text-xs flex items-center justify-center shadow-md shadow-emerald-500/30">✓</span>
                                    <h2 class="text-lg font-bold text-slate-900">Customer Account</h2>
                                </div>
                                <span class="text-xs text-emerald-700 bg-emerald-50 border border-emerald-200 font-bold px-3 py-1 rounded-full flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Verified & Linked
                                </span>
                            </div>

                            <div class="p-4 bg-purple-50/60 border border-purple-200/80 rounded-2xl flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-12 h-12 rounded-xl bg-[#673DE6] text-white font-extrabold flex items-center justify-center text-lg shadow-md shadow-[#673DE6]/25">
                                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-xs text-purple-900 font-semibold">Active Client Account</div>
                                        <div class="text-base font-extrabold text-slate-900">{{ $user->name }}</div>
                                        <div class="text-xs text-slate-600 font-mono">{{ $user->email }}</div>
                                    </div>
                                </div>
                                <a href="{{ url('/customer/profile') }}" target="_blank" class="text-xs text-[#673DE6] hover:text-[#5428D8] font-bold underline transition-colors">
                                    Manage Profile
                                </a>
                            </div>
                        </div>

                        <!-- CARD 2: CONFIGURED SERVER SPECS REVIEW -->
                        <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-soft-sm">
                            <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-full bg-[#673DE6] text-white font-bold text-xs flex items-center justify-center shadow-md shadow-[#673DE6]/30">⚙️</span>
                                    <h2 class="text-lg font-bold text-slate-900">Configured Cloud Instance</h2>
                                </div>
                                <a href="{{ route('checkout.show', ['package' => $package->id, 'cycle' => $cycle]) }}" class="inline-flex items-center gap-1 text-xs font-bold text-[#673DE6] hover:text-[#5428D8] bg-purple-50 hover:bg-purple-100 px-3 py-1.5 rounded-xl transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    <span>Edit Configuration</span>
                                </a>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Plan & Billing Cycle</div>
                                    <div class="text-sm font-extrabold text-slate-900">{{ $package->name }}</div>
                                    <div class="text-xs text-[#673DE6] font-semibold mt-0.5">{{ $cycleLabel }}</div>
                                </div>

                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Operating System</div>
                                    <div class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                                        <span>🐧</span>
                                        <span>{{ $pendingOrder['os'] ?? 'Ubuntu 24.04 LTS' }}</span>
                                    </div>
                                    <div class="text-xs text-slate-500 mt-0.5">Automated image install</div>
                                </div>

                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Datacenter Region</div>
                                    <div class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                                        <span>📍</span>
                                        <span>{{ $pendingOrder['datacenter'] ?? 'US East (New York)' }}</span>
                                    </div>
                                    <div class="text-xs text-emerald-600 font-semibold mt-0.5">Low-latency premium tier</div>
                                </div>

                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Hostname</div>
                                    <div class="text-sm font-extrabold text-slate-900 font-mono truncate">
                                        {{ $pendingOrder['hostname'] ?: 'vps-auto.vortexcloud.net' }}
                                    </div>
                                    <div class="text-xs text-slate-500 mt-0.5">Automated DNS binding</div>
                                </div>
                            </div>

                            @if(!empty($pendingOrder['auto_backup']) || !empty($pendingOrder['private_networking']))
                                <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap gap-2">
                                    @if(!empty($pendingOrder['auto_backup']))
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold">
                                            <span>🛡️</span> Daily Automated Backups
                                        </span>
                                    @endif
                                    @if(!empty($pendingOrder['private_networking']))
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold">
                                            <span>🔒</span> 10Gbps Private VPC Networking
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- CARD 3: PAYMENT METHOD -->
                        <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-soft-sm space-y-6">
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-xl bg-gradient-to-tr from-[#673DE6] to-[#8B5CF6] text-white font-bold text-sm flex items-center justify-center shadow-md shadow-[#673DE6]/25">💳</span>
                                    <div>
                                        <h2 class="text-lg font-extrabold text-slate-900">Select Payment Method (পেমেন্ট মেথড)</h2>
                                        <p class="text-xs text-slate-500">বিকাশ, নগদ, রকেট, বাইনান্স বা কার্ড দিয়ে পেমেন্ট করুন</p>
                                    </div>
                                </div>
                                <span class="text-xs text-emerald-600 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full font-bold flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Manual & Instant Verified
                                </span>
                            </div>

                            <!-- Payment Method Tabs Grid -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <!-- bKash -->
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_type" value="bkash" class="peer sr-only" {{ $defaultPaymentMethod === 'bkash' ? 'checked' : '' }} onchange="togglePaymentMethod('bkash')">
                                    <div class="p-3.5 rounded-2xl border-2 border-slate-200 peer-checked:border-[#E2136E] peer-checked:bg-pink-50/60 peer-checked:shadow-sm hover:border-pink-300 transition-all text-center flex flex-col items-center justify-center h-full">
                                        <span class="text-2xl mb-1">🌸</span>
                                        <span class="block text-slate-900 font-extrabold text-sm">বিকাশ (bKash)</span>
                                        <span class="text-[10px] font-bold text-[#E2136E]">Personal / Send Money</span>
                                    </div>
                                </label>

                                <!-- Nagad -->
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_type" value="nagad" class="peer sr-only" {{ $defaultPaymentMethod === 'nagad' ? 'checked' : '' }} onchange="togglePaymentMethod('nagad')">
                                    <div class="p-3.5 rounded-2xl border-2 border-slate-200 peer-checked:border-[#F7931A] peer-checked:bg-orange-50/60 peer-checked:shadow-sm hover:border-orange-300 transition-all text-center flex flex-col items-center justify-center h-full">
                                        <span class="text-2xl mb-1">🔶</span>
                                        <span class="block text-slate-900 font-extrabold text-sm">নগদ (Nagad)</span>
                                        <span class="text-[10px] font-bold text-[#F7931A]">Send Money</span>
                                    </div>
                                </label>

                                <!-- Rocket -->
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_type" value="rocket" class="peer sr-only" {{ $defaultPaymentMethod === 'rocket' ? 'checked' : '' }} onchange="togglePaymentMethod('rocket')">
                                    <div class="p-3.5 rounded-2xl border-2 border-slate-200 peer-checked:border-[#8C3494] peer-checked:bg-purple-50/60 peer-checked:shadow-sm hover:border-purple-300 transition-all text-center flex flex-col items-center justify-center h-full">
                                        <span class="text-2xl mb-1">🚀</span>
                                        <span class="block text-slate-900 font-extrabold text-sm">রকেট (Rocket)</span>
                                        <span class="text-[10px] font-bold text-[#8C3494]">DBBL Mobile Banking</span>
                                    </div>
                                </label>

                                <!-- Binance Pay / Crypto -->
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_type" value="binance" class="peer sr-only" {{ $defaultPaymentMethod === 'binance' ? 'checked' : '' }} onchange="togglePaymentMethod('binance')">
                                    <div class="p-3.5 rounded-2xl border-2 border-slate-200 peer-checked:border-[#F3BA2F] peer-checked:bg-amber-50/60 peer-checked:shadow-sm hover:border-amber-300 transition-all text-center flex flex-col items-center justify-center h-full">
                                        <span class="text-2xl mb-1">🟡</span>
                                        <span class="block text-slate-900 font-extrabold text-sm">Binance Pay</span>
                                        <span class="text-[10px] font-bold text-[#D97706]">USDT / Crypto</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Payment Detail Panes -->

                            <!-- 1. BKASH CONTAINER -->
                            <div id="pane-bkash" class="payment-pane {{ $defaultPaymentMethod === 'bkash' ? '' : 'hidden' }} space-y-4 p-5 rounded-2xl bg-gradient-to-b from-pink-50/80 to-white border border-pink-200">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-pink-100">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-8 h-8 rounded-full bg-[#E2136E] text-white flex items-center justify-center font-bold text-sm">Bk</span>
                                        <div>
                                            <h3 class="text-sm font-bold text-pink-950">বিকাশ সেন্ড মানি (Personal Account)</h3>
                                            <p class="text-xs text-pink-700">বিকাশ অ্যাপ বা *247# থেকে সেন্ড মানি করুন</p>
                                        </div>
                                    </div>
                                    <div class="text-left sm:text-right bg-white sm:bg-transparent p-2 sm:p-0 rounded-xl border sm:border-0 border-pink-200">
                                        <span class="text-[11px] text-slate-500 block">মোট প্রদেয় টাকা:</span>
                                        <span class="text-lg font-black text-[#E2136E]">৳ {{ $bdtFormatted }} BDT</span>
                                        <span class="text-[10px] text-slate-400 block">(${{ number_format($finalTotal, 2) }} × ৳{{ $rateBdt }})</span>
                                    </div>
                                </div>

                                <div class="bg-white p-4 rounded-xl border border-pink-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                    <div>
                                        <span class="text-xs text-slate-500 font-bold block uppercase tracking-wider">বিকাশ নাম্বার (Send Money Number):</span>
                                        <span class="text-lg font-black text-slate-900 font-mono select-all">{{ $bkash->account_number ?? '01619789895' }}</span>
                                        <span class="inline-block ml-2 text-[10px] font-extrabold px-2 py-0.5 rounded bg-pink-100 text-[#E2136E] uppercase">{{ $bkash->account_type ?? 'Personal' }}</span>
                                    </div>
                                    <button type="button" onclick="copyToClipboard('{{ $bkash->account_number ?? '01619789895' }}', 'bkash-btn')" id="bkash-btn" class="w-full sm:w-auto px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white text-xs font-bold rounded-lg transition-all flex items-center justify-center gap-1.5 shadow-sm">
                                        <span>📋</span> <span>Copy Number</span>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">আপনার বিকাশ নাম্বার (Sender Mobile) <span class="text-rose-500">*</span></label>
                                        <input type="text" name="sender_number" placeholder="017XXXXXXXX" value="{{ old('sender_number') }}" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-[#E2136E] focus:border-[#E2136E] outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">বিকাশ TrxID (Transaction ID) <span class="text-rose-500">*</span></label>
                                        <input type="text" name="transaction_id" placeholder="e.g. BL99XX28" value="{{ old('transaction_id') }}" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 uppercase font-mono tracking-wider focus:ring-2 focus:ring-[#E2136E] focus:border-[#E2136E] outline-none">
                                    </div>
                                </div>

                                <div class="flex items-center justify-between text-xs text-slate-600 bg-white/70 p-3 rounded-xl border border-pink-100">
                                    <span class="flex items-center gap-1">📞 যে কোন সমস্যায় কল করুন: <strong class="text-slate-900 font-mono">{{ $supportPhone }}</strong></span>
                                    <a href="tel:{{ $supportPhone }}" class="text-pink-600 font-bold hover:underline">Direct Call</a>
                                </div>
                            </div>

                            <!-- 2. NAGAD CONTAINER -->
                            <div id="pane-nagad" class="payment-pane {{ $defaultPaymentMethod === 'nagad' ? '' : 'hidden' }} space-y-4 p-5 rounded-2xl bg-gradient-to-b from-orange-50/80 to-white border border-orange-200">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-orange-100">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-8 h-8 rounded-full bg-[#F7931A] text-white flex items-center justify-center font-bold text-sm">Ng</span>
                                        <div>
                                            <h3 class="text-sm font-bold text-orange-950">নগদ সেন্ড মানি (Personal Account)</h3>
                                            <p class="text-xs text-orange-700">নগদ অ্যাপ বা *167# থেকে সেন্ড মানি করুন</p>
                                        </div>
                                    </div>
                                    <div class="text-left sm:text-right bg-white sm:bg-transparent p-2 sm:p-0 rounded-xl border sm:border-0 border-orange-200">
                                        <span class="text-[11px] text-slate-500 block">মোট প্রদেয় টাকা:</span>
                                        <span class="text-lg font-black text-[#F7931A]">৳ {{ $bdtFormatted }} BDT</span>
                                        <span class="text-[10px] text-slate-400 block">(${{ number_format($finalTotal, 2) }} × ৳{{ $rateBdt }})</span>
                                    </div>
                                </div>

                                <div class="bg-white p-4 rounded-xl border border-orange-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                    <div>
                                        <span class="text-xs text-slate-500 font-bold block uppercase tracking-wider">নগদ নাম্বার (Send Money Number):</span>
                                        <span class="text-lg font-black text-slate-900 font-mono select-all">{{ $nagad->account_number ?? '01619789895' }}</span>
                                        <span class="inline-block ml-2 text-[10px] font-extrabold px-2 py-0.5 rounded bg-orange-100 text-[#F7931A] uppercase">{{ $nagad->account_type ?? 'Personal' }}</span>
                                    </div>
                                    <button type="button" onclick="copyToClipboard('{{ $nagad->account_number ?? '01619789895' }}', 'nagad-btn')" id="nagad-btn" class="w-full sm:w-auto px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-lg transition-all flex items-center justify-center gap-1.5 shadow-sm">
                                        <span>📋</span> <span>Copy Number</span>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">আপনার নগদ নাম্বার (Sender Mobile) <span class="text-rose-500">*</span></label>
                                        <input type="text" name="sender_number_ng" placeholder="017XXXXXXXX" value="{{ old('sender_number') }}" oninput="document.querySelector('input[name=sender_number]').value=this.value" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-[#F7931A] focus:border-[#F7931A] outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">নগদ TrxID (Transaction ID) <span class="text-rose-500">*</span></label>
                                        <input type="text" name="transaction_id_ng" placeholder="e.g. 71A8XXXX" value="{{ old('transaction_id') }}" oninput="document.querySelector('input[name=transaction_id]').value=this.value" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 uppercase font-mono tracking-wider focus:ring-2 focus:ring-[#F7931A] focus:border-[#F7931A] outline-none">
                                    </div>
                                </div>

                                <div class="flex items-center justify-between text-xs text-slate-600 bg-white/70 p-3 rounded-xl border border-orange-100">
                                    <span class="flex items-center gap-1">📞 যে কোন সমস্যায় কল করুন: <strong class="text-slate-900 font-mono">{{ $supportPhone }}</strong></span>
                                    <a href="tel:{{ $supportPhone }}" class="text-orange-600 font-bold hover:underline">Direct Call</a>
                                </div>
                            </div>

                            <!-- 3. ROCKET CONTAINER -->
                            <div id="pane-rocket" class="payment-pane {{ $defaultPaymentMethod === 'rocket' ? '' : 'hidden' }} space-y-4 p-5 rounded-2xl bg-gradient-to-b from-purple-50/80 to-white border border-purple-200">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-purple-100">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-8 h-8 rounded-full bg-[#8C3494] text-white flex items-center justify-center font-bold text-sm">Rk</span>
                                        <div>
                                            <h3 class="text-sm font-bold text-purple-950">রকেট সেন্ড মানি (Personal Account)</h3>
                                            <p class="text-xs text-purple-700">রকেট অ্যাপ বা *322# থেকে সেন্ড মানি করুন</p>
                                        </div>
                                    </div>
                                    <div class="text-left sm:text-right bg-white sm:bg-transparent p-2 sm:p-0 rounded-xl border sm:border-0 border-purple-200">
                                        <span class="text-[11px] text-slate-500 block">মোট প্রদেয় টাকা:</span>
                                        <span class="text-lg font-black text-[#8C3494]">৳ {{ $bdtFormatted }} BDT</span>
                                        <span class="text-[10px] text-slate-400 block">(${{ number_format($finalTotal, 2) }} × ৳{{ $rateBdt }})</span>
                                    </div>
                                </div>

                                <div class="bg-white p-4 rounded-xl border border-purple-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                    <div>
                                        <span class="text-xs text-slate-500 font-bold block uppercase tracking-wider">রকেট নাম্বার (Send Money Number):</span>
                                        <span class="text-lg font-black text-slate-900 font-mono select-all">{{ $rocket->account_number ?? '016197898958' }}</span>
                                        <span class="inline-block ml-2 text-[10px] font-extrabold px-2 py-0.5 rounded bg-purple-100 text-[#8C3494] uppercase">{{ $rocket->account_type ?? 'Personal' }}</span>
                                    </div>
                                    <button type="button" onclick="copyToClipboard('{{ $rocket->account_number ?? '016197898958' }}', 'rocket-btn')" id="rocket-btn" class="w-full sm:w-auto px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-lg transition-all flex items-center justify-center gap-1.5 shadow-sm">
                                        <span>📋</span> <span>Copy Number</span>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">আপনার রকেট নাম্বার (Sender Mobile) <span class="text-rose-500">*</span></label>
                                        <input type="text" name="sender_number_rk" placeholder="017XXXXXXXXX" value="{{ old('sender_number') }}" oninput="document.querySelector('input[name=sender_number]').value=this.value" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-[#8C3494] focus:border-[#8C3494] outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">রকেট TrxID (Transaction ID) <span class="text-rose-500">*</span></label>
                                        <input type="text" name="transaction_id_rk" placeholder="e.g. 9812XXXX" value="{{ old('transaction_id') }}" oninput="document.querySelector('input[name=transaction_id]').value=this.value" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 uppercase font-mono tracking-wider focus:ring-2 focus:ring-[#8C3494] focus:border-[#8C3494] outline-none">
                                    </div>
                                </div>

                                <div class="flex items-center justify-between text-xs text-slate-600 bg-white/70 p-3 rounded-xl border border-purple-100">
                                    <span class="flex items-center gap-1">📞 যে কোন সমস্যায় কল করুন: <strong class="text-slate-900 font-mono">{{ $supportPhone }}</strong></span>
                                    <a href="tel:{{ $supportPhone }}" class="text-purple-600 font-bold hover:underline">Direct Call</a>
                                </div>
                            </div>

                            <!-- 4. BINANCE PAY CONTAINER -->
                            <div id="pane-binance" class="payment-pane {{ $defaultPaymentMethod === 'binance' ? '' : 'hidden' }} space-y-4 p-5 rounded-2xl bg-gradient-to-b from-amber-50/80 to-white border border-amber-200">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-amber-100">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-8 h-8 rounded-full bg-[#F3BA2F] text-slate-900 flex items-center justify-center font-bold text-sm">BN</span>
                                        <div>
                                            <h3 class="text-sm font-bold text-amber-950">Binance Pay & Crypto (USDT)</h3>
                                            <p class="text-xs text-amber-700">Binance Pay ID অথবা USDT TRC-20 দিয়ে সেন্ড করুন</p>
                                        </div>
                                    </div>
                                    <div class="text-left sm:text-right bg-white sm:bg-transparent p-2 sm:p-0 rounded-xl border sm:border-0 border-amber-200">
                                        <span class="text-[11px] text-slate-500 block">Total Payable:</span>
                                        <span class="text-lg font-black text-amber-700">${{ number_format($finalTotal, 2) }} USDT</span>
                                    </div>
                                </div>

                                <div class="bg-white p-4 rounded-xl border border-amber-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <span class="text-xs text-slate-500 font-bold block uppercase tracking-wider">USDT (TRC-20) / Binance Pay Address:</span>
                                        <span class="text-sm sm:text-base font-black text-slate-900 font-mono break-all select-all">{{ $binance->account_number ?? 'TPFMfZU4cPcfi3ivmUECDj9bYy5aWdZ4EE' }}</span>
                                    </div>
                                    <button type="button" onclick="copyToClipboard('{{ $binance->account_number ?? 'TPFMfZU4cPcfi3ivmUECDj9bYy5aWdZ4EE' }}', 'binance-btn')" id="binance-btn" class="w-full sm:w-auto px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-900 text-xs font-bold rounded-lg transition-all flex items-center justify-center gap-1.5 shadow-sm shrink-0">
                                        <span>📋</span> <span>Copy Address</span>
                                    </button>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Binance Pay Order ID অথবা Crypto TxID <span class="text-rose-500">*</span></label>
                                    <input type="text" name="transaction_id_bn" placeholder="e.g. 523829104 or 0x3f982..." value="{{ old('transaction_id') }}" oninput="document.querySelector('input[name=transaction_id]').value=this.value" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 font-mono tracking-wider focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
                                </div>
                            </div>

                            <!-- Optional Notes -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Payment Reference / Notes (ঐচ্ছিক নোট)</label>
                                <input type="text" name="payment_notes" value="{{ old('payment_notes') }}" placeholder="e.g. VPS Order for My Website" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:ring-2 focus:ring-[#673DE6] focus:border-[#673DE6] outline-none">
                            </div>

                            <!-- Coupon Code Input -->
                            <div class="pt-4 border-t border-slate-100">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Have a Promotional Code?</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" name="coupon_code" id="coupon_code" value="{{ old('coupon_code', $pendingOrder['coupon_code'] ?? '') }}" placeholder="e.g. VORTEX20" class="flex-grow bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-900 uppercase font-mono tracking-wider focus:ring-2 focus:ring-[#673DE6] focus:border-[#673DE6] outline-none">
                                    <button type="button" onclick="applyCouponClient()" class="bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold px-4 py-2.5 rounded-xl text-xs transition-colors shrink-0">
                                        Apply
                                    </button>
                                </div>
                                <div id="coupon-feedback" class="text-xs mt-1.5 font-medium {{ $appliedCoupon ? 'text-emerald-600 block' : 'hidden' }}">
                                    @if($appliedCoupon)
                                        ✓ Coupon "{{ $appliedCoupon->code }}" applied (Saved ${{ number_format($couponDiscount, 2) }})
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- RIGHT COLUMN: Sticky Order Summary (4-5 Cols) -->
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
                                        <span class="font-bold text-slate-900">{{ $storageVal }}</span>
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
                                        <span class="font-bold text-slate-900 truncate max-w-[150px]">
                                            {{ $pendingOrder['os'] ?? 'Ubuntu 24.04 LTS' }}
                                        </span>
                                    </li>
                                    <li class="flex items-center justify-between">
                                        <span class="text-slate-500 flex items-center gap-2">
                                            <span class="text-purple-600">📍</span> Datacenter
                                        </span>
                                        <span class="font-bold text-slate-900 truncate max-w-[150px]">
                                            {{ $pendingOrder['datacenter'] ?? 'US East (New York)' }}
                                        </span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Final Price Calculation Breakdown -->
                            <div class="p-6 space-y-3">
                                @if(isset($invoice) && $invoice)
                                    <div class="flex items-center justify-between text-xs pb-2.5 mb-1 border-b border-slate-100">
                                        <span class="text-slate-500 font-medium">Invoice Reference</span>
                                        <span class="font-mono font-bold text-[#673DE6] bg-purple-50 px-2 py-0.5 rounded-md border border-purple-200/80">#{{ $invoice->invoice_number }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-slate-600">Period Subtotal</span>
                                    <span class="font-bold text-slate-900">${{ number_format($baseTotal, 2) }}</span>
                                </div>

                                @if($cycleDiscountAmount > 0)
                                    <div class="flex items-center justify-between text-sm text-emerald-600 font-semibold">
                                        <span>Term Discount ({{ $cycleDiscountPercent }}%)</span>
                                        <span>-${{ number_format($cycleDiscountAmount, 2) }}</span>
                                    </div>
                                @endif

                                @if($couponDiscount > 0)
                                    <div class="flex items-center justify-between text-sm text-purple-600 font-semibold">
                                        <span>Coupon Discount</span>
                                        <span>-${{ number_format($couponDiscount, 2) }}</span>
                                    </div>
                                @endif

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
                                        <div class="text-3xl font-extrabold text-slate-900 tracking-tight">
                                            ${{ number_format($finalTotal, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Main CTA Submit Button -->
                            <div class="p-6 pt-0">
                                <button type="submit" id="submit-button" class="btn-shimmer w-full bg-[#673DE6] hover:bg-[#5428D8] text-white font-extrabold py-4 px-6 rounded-xl shadow-xl shadow-[#673DE6]/30 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2 text-base cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="w-5 h-5 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <span id="button-text">
                                        @if($defaultPaymentMethod === 'bkash')
                                            Place Order with bKash (৳{{ $bdtFormatted }})
                                        @elseif($defaultPaymentMethod === 'nagad')
                                            Place Order with Nagad (৳{{ $bdtFormatted }})
                                        @elseif($defaultPaymentMethod === 'rocket')
                                            Place Order with Rocket (৳{{ $bdtFormatted }})
                                        @elseif($defaultPaymentMethod === 'binance')
                                            Place Order with Binance Pay (${{ number_format($finalTotal, 2) }})
                                        @else
                                            Pay ${{ number_format($finalTotal, 2) }} & Deploy Server
                                        @endif
                                    </span>
                                    <svg id="spinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </button>
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
                                <span><strong>256-Bit SSL Encryption</strong> enterprise secure</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span><strong>Instant Provisioning Engine</strong> active</span>
                            </div>
                        </div>

                    </div>

                </div>
            </form>

        </div>
    </div>

    <!-- Scripts -->
    <script>
        function togglePaymentMethod(method) {
            document.querySelectorAll('.payment-pane').forEach(p => p.classList.add('hidden'));
            const targetPane = document.getElementById('pane-' + method);
            if (targetPane) targetPane.classList.remove('hidden');

            const btnText = document.getElementById('button-text');
            if (btnText) {
                if (method === 'bkash') {
                    btnText.textContent = 'Place Order with bKash (৳{{ $bdtFormatted }})';
                } else if (method === 'nagad') {
                    btnText.textContent = 'Place Order with Nagad (৳{{ $bdtFormatted }})';
                } else if (method === 'rocket') {
                    btnText.textContent = 'Place Order with Rocket (৳{{ $bdtFormatted }})';
                } else if (method === 'binance') {
                    btnText.textContent = 'Place Order with Binance Pay (${{ number_format($finalTotal, 2) }})';
                } else {
                    btnText.textContent = 'Pay ${{ number_format($finalTotal, 2) }} & Deploy Server';
                }
            }
        }

        function copyToClipboard(text, btnId) {
            navigator.clipboard.writeText(text).then(() => {
                const btn = document.getElementById(btnId);
                if (btn) {
                    const originalHtml = btn.innerHTML;
                    btn.innerHTML = '<span>✓</span> <span>Copied!</span>';
                    btn.classList.add('bg-emerald-600');
                    setTimeout(() => {
                        btn.innerHTML = originalHtml;
                        btn.classList.remove('bg-emerald-600');
                    }, 2000);
                }
            }).catch(() => {
                prompt('Copy this number:', text);
            });
        }

        function applyCouponClient() {
            const code = document.getElementById('coupon_code').value.trim();
            const feedback = document.getElementById('coupon-feedback');
            
            if (!code) {
                feedback.className = 'text-xs mt-1.5 font-medium text-rose-600 block';
                feedback.textContent = 'Please enter a coupon code.';
                return;
            }

            feedback.className = 'text-xs mt-1.5 font-medium text-emerald-600 block';
            feedback.textContent = 'Coupon "' + code.toUpperCase() + '" will be verified upon placing order.';
        }

        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById('checkout-payment-form');
            if (form) {
                form.addEventListener('submit', function(event) {
                    const checkedRadio = document.querySelector('input[name="payment_type"]:checked');
                    if (!checkedRadio) {
                        event.preventDefault();
                        alert('অনুগ্রহ করে একটি পেমেন্ট মেথড নির্বাচন করুন (Select a payment method).');
                        return;
                    }
                    const paymentType = checkedRadio.value;

                    // Sync inputs from active tab if needed
                    const senderInputs = {
                        nagad: 'input[name="sender_number_ng"]',
                        rocket: 'input[name="sender_number_rk"]'
                    };
                    const trxInputs = {
                        nagad: 'input[name="transaction_id_ng"]',
                        rocket: 'input[name="transaction_id_rk"]',
                        binance: 'input[name="transaction_id_bn"]'
                    };

                    const mainSender = document.querySelector('input[name="sender_number"]');
                    const mainTrx = document.querySelector('input[name="transaction_id"]');

                    if (senderInputs[paymentType]) {
                        const val = document.querySelector(senderInputs[paymentType])?.value;
                        if (val && mainSender) mainSender.value = val;
                    }
                    if (trxInputs[paymentType]) {
                        const val = document.querySelector(trxInputs[paymentType])?.value;
                        if (val && mainTrx) mainTrx.value = val;
                    }

                    document.getElementById('submit-button').disabled = true;
                    document.getElementById('button-text').textContent = 'Submitting Payment Proof...';
                    document.getElementById('spinner').classList.remove('hidden');
                });
            }
        });
    </script>
</x-app-layout>
