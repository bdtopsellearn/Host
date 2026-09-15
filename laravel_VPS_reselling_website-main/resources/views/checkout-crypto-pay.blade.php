<x-app-layout 
    title="Cryptocurrency Payment - Invoice #{{ $invoice->invoice_number }} - VortexCloud" 
    description="Complete your cryptocurrency payment for instant cloud VPS deployment." 
    headerVariant="solid"
    robots="noindex, nofollow">

    @php
        $user = auth()->user();
        $order = $invoice->order;
        $specsJson = $service && is_array($service->specs_snapshot) ? $service->specs_snapshot : [];
        $totalAmount = number_format($invoice->total, 2);

        $trc20Address = $wallets['usdt_trc20'] ?? 'TPFMfZU4cPcfi3ivmUECDj9bYy5aWdZ4EE';
        $usdcPolyAddress = $wallets['usdc_polygon'] ?? '0x73F701571238739aBce996b6D7358599411FE233';
        $usdtPolyAddress = $wallets['usdt_polygon'] ?? '0x73F701571238739aBce996b6D7358599411FE233';

        $activeNetwork = old('crypto_network', $invoice->crypto_network ?? 'usdt_trc20');
        $hasSubmittedTxid = !empty($invoice->crypto_txid);
    @endphp

    <div class="py-10 md:py-16 bg-slate-50/70 min-h-[90vh]">
        <div class="w-full max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-12">
            
            <!-- Top Funnel Header -->
            <div class="text-center max-w-2xl mx-auto mb-10">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-purple-50 border border-purple-200 text-[#673DE6] text-xs font-bold uppercase tracking-wider mb-3 shadow-soft-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Step 3: Crypto Payment</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Complete Your Crypto Transfer
                </h1>
                <p class="text-sm sm:text-base text-slate-600 mt-2">
                    Send exact USDT or USDC to our verified secure address. Server provisioning activates automatically upon confirmation.
                </p>
            </div>

            <!-- Standard 3-Step Funnel Stepper Card -->
            <div class="max-w-3xl mx-auto mb-10">
                <div class="bg-white border border-slate-200/90 rounded-2xl p-2 sm:p-2.5 shadow-soft-sm">
                    <div class="grid grid-cols-3 gap-1.5 sm:gap-3">
                        
                        <!-- Step 1: Completed -->
                        <div class="flex items-center gap-2 sm:gap-3 p-2 sm:p-2.5 rounded-xl bg-emerald-50/80 border border-emerald-200/80 text-emerald-700">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-emerald-500 text-white font-black text-xs sm:text-sm flex items-center justify-center shadow-md shadow-emerald-500/25 shrink-0">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="min-w-0">
                                <span class="hidden sm:block text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 leading-none mb-0.5">Completed</span>
                                <span class="block text-xs sm:text-sm font-bold text-slate-900 truncate">1. Configure</span>
                            </div>
                        </div>

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

                        <!-- Step 3: Active Crypto -->
                        <div class="flex items-center gap-2 sm:gap-3 p-2 sm:p-2.5 rounded-xl bg-purple-50/90 border border-purple-200/80 text-[#673DE6]">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-[#673DE6] text-white font-extrabold text-xs sm:text-sm flex items-center justify-center shadow-md shadow-[#673DE6]/30 shrink-0">
                                3
                            </div>
                            <div class="min-w-0">
                                <span class="hidden sm:block text-[10px] font-extrabold uppercase tracking-wider text-purple-600 leading-none mb-0.5">{{ $hasSubmittedTxid ? 'Under Review' : 'Awaiting Payment' }}</span>
                                <span class="block text-xs sm:text-sm font-bold text-slate-900 truncate">3. Crypto Transfer</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Global Feedback Toast / Alerts -->
            @if(session('success'))
                <!-- Confirmation Modal Popup with 7-Second Progress Bar -->
                <div id="crypto-success-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-md animate-fade-in">
                    <div class="relative w-full max-w-md p-6 sm:p-8 bg-white border border-slate-200 rounded-3xl shadow-2xl text-center">
                        
                        <!-- Animated Emerald Checkmark Badge -->
                        <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center ring-8 ring-emerald-50 shadow-md">
                            <svg class="w-9 h-9 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>

                        <!-- Modal Title & Details -->
                        <h3 class="text-2xl font-black text-slate-900 tracking-tight">Payment Proof Submitted!</h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-2.5 leading-relaxed">
                            We've received your transaction hash for <strong class="text-slate-900 font-bold">#{{ $invoice->invoice_number }}</strong>. Our engineering team is verifying the blockchain arrival to spin up your cloud server.
                        </p>

                        <!-- TxID Capsule -->
                        @if(!empty($invoice->crypto_txid))
                            <div class="mt-4 p-3 bg-slate-50 border border-slate-200/80 rounded-xl font-mono text-[11px] text-slate-700 truncate select-all flex items-center justify-center gap-1.5">
                                <span class="font-bold text-slate-400">TxID:</span>
                                <span class="font-semibold text-slate-900">{{ $invoice->crypto_txid }}</span>
                            </div>
                        @endif

                        <!-- Progress Bar Section (Visual Only - No Numerical Time) -->
                        <div class="mt-6 pt-5 border-t border-slate-100">
                            <div class="flex items-center justify-between text-xs font-bold mb-2">
                                <span class="text-slate-500">Redirecting to Client Portal...</span>
                                <span class="text-purple-600 flex items-center">
                                    <svg class="animate-spin h-3.5 w-3.5 text-[#673DE6]" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </div>

                            <!-- Animated Visual Progress Bar Track -->
                            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden p-0.5 border border-slate-200/60">
                                <div id="modal-progress-bar" 
                                     class="h-full bg-gradient-to-r from-[#673DE6] via-[#7E57C2] to-[#10B981] rounded-full shadow-sm"
                                     style="width: 0%;"></div>
                            </div>
                        </div>

                        <!-- Direct Navigation Buttons -->
                        <div class="mt-6 space-y-2">
                            <a href="{{ url('/customer/invoices') }}" 
                               id="modal-redirect-btn"
                               class="btn-shimmer w-full bg-[#673DE6] hover:bg-[#5428D8] text-white font-extrabold py-3.5 px-5 rounded-xl shadow-lg shadow-[#673DE6]/25 transition-all inline-flex items-center justify-center gap-2 text-xs sm:text-sm">
                                <span>Go to Client Portal Now</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                            <button type="button" 
                                    onclick="window.dismissCryptoSuccessModal()" 
                                    class="text-xs text-slate-400 hover:text-slate-600 font-semibold transition-colors py-1">
                                Stay on this payment page
                            </button>
                        </div>

                    </div>
                </div>

                <!-- 7.5-Second Visual Progress Bar & Auto-Redirect Script -->
                <script>
                    (function() {
                        const totalDurationMs = 7500;
                        const startTime = Date.now();
                        const progressBar = document.getElementById('modal-progress-bar');
                        const targetUrl = "{{ url('/customer/invoices') }}";
                        let timerActive = true;

                        window.dismissCryptoSuccessModal = function() {
                            timerActive = false;
                            const modal = document.getElementById('crypto-success-modal');
                            if (modal) {
                                modal.style.opacity = '0';
                                modal.style.transition = 'opacity 0.25s ease';
                                setTimeout(() => modal.remove(), 250);
                            }
                        };

                        const updateProgress = function() {
                            if (!timerActive) return;

                            const elapsedMs = Date.now() - startTime;

                            if (progressBar) {
                                const percentage = Math.min(100, (elapsedMs / totalDurationMs) * 100);
                                progressBar.style.width = percentage.toFixed(2) + '%';
                            }

                            if (elapsedMs >= totalDurationMs) {
                                timerActive = false;
                                window.location.href = targetUrl;
                            } else {
                                requestAnimationFrame(updateProgress);
                            }
                        };

                        requestAnimationFrame(updateProgress);
                    })();
                </script>

                <!-- In-page alert banner (fallback) -->
                <div class="max-w-4xl mx-auto mb-8 bg-emerald-50 border border-emerald-200 text-emerald-900 p-5 rounded-2xl shadow-soft-sm flex items-start gap-3">
                    <span class="text-xl">✅</span>
                    <div>
                        <div class="font-extrabold text-sm text-emerald-900">Proof Submitted Successfully</div>
                        <div class="text-xs sm:text-sm text-emerald-700 mt-0.5">{{ session('success') }}</div>
                    </div>
                </div>
            @endif

            @if(session('info'))
                <div class="max-w-4xl mx-auto mb-8 bg-purple-50 border border-purple-200 text-purple-900 p-4 rounded-2xl shadow-soft-sm flex items-center gap-3">
                    <span class="text-lg">ℹ️</span>
                    <div class="text-xs sm:text-sm text-purple-800 font-medium">{{ session('info') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="max-w-4xl mx-auto mb-8 bg-rose-50 border border-rose-200 text-rose-800 p-5 rounded-2xl shadow-soft-sm">
                    <div class="font-bold text-sm text-rose-900 mb-1">Please correct the following:</div>
                    <ul class="list-disc pl-5 text-xs text-rose-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start w-full">
                
                <!-- LEFT COLUMN: Interactive Crypto Payment Station (7-8 Cols) -->
                <div class="lg:col-span-7 xl:col-span-8 space-y-8">
                    
                    <!-- CARD 1: WALLET & QR PAYMENT STATION -->
                    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-soft-sm">
                        
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 mb-6 border-b border-slate-100">
                            <div>
                                <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                                    <span>💎</span> Select Cryptocurrency Network
                                </h2>
                                <p class="text-xs text-slate-500 mt-0.5">Pick your preferred stablecoin & network below:</p>
                            </div>
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full self-start sm:self-auto">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Live 1:1 USD Rate
                            </span>
                        </div>

                        <!-- 3-Network Switcher Tabs -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                            
                            <!-- TAB 1: USDT (Tron) -->
                            <button type="button" 
                                    onclick="switchCryptoNetwork('usdt_trc20')"
                                    id="btn-network-usdt_trc20"
                                    class="network-tab-btn p-3.5 rounded-2xl border-2 transition-all text-left flex flex-col justify-between {{ $activeNetwork === 'usdt_trc20' ? 'border-[#673DE6] bg-purple-50/60 shadow-soft-sm' : 'border-slate-200 hover:border-purple-200 bg-white' }}">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xl">🟢</span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $activeNetwork === 'usdt_trc20' ? 'bg-[#673DE6] text-white' : 'bg-slate-100 text-slate-600' }}" id="badge-usdt_trc20">
                                        TRC-20
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-extrabold text-slate-900">USDT (Tron)</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Fast & Low Gas Fee</div>
                                </div>
                            </button>

                            <!-- TAB 2: USDC (Polygon) -->
                            <button type="button" 
                                    onclick="switchCryptoNetwork('usdc_polygon')"
                                    id="btn-network-usdc_polygon"
                                    class="network-tab-btn p-3.5 rounded-2xl border-2 transition-all text-left flex flex-col justify-between {{ $activeNetwork === 'usdc_polygon' ? 'border-[#673DE6] bg-purple-50/60 shadow-soft-sm' : 'border-slate-200 hover:border-purple-200 bg-white' }}">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xl">🟣</span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $activeNetwork === 'usdc_polygon' ? 'bg-[#673DE6] text-white' : 'bg-slate-100 text-slate-600' }}" id="badge-usdc_polygon">
                                        Polygon
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-extrabold text-slate-900">USDC (Polygon)</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Instant & &lt;$0.01 fee</div>
                                </div>
                            </button>

                            <!-- TAB 3: USDT (Polygon) -->
                            <button type="button" 
                                    onclick="switchCryptoNetwork('usdt_polygon')"
                                    id="btn-network-usdt_polygon"
                                    class="network-tab-btn p-3.5 rounded-2xl border-2 transition-all text-left flex flex-col justify-between {{ $activeNetwork === 'usdt_polygon' ? 'border-[#673DE6] bg-purple-50/60 shadow-soft-sm' : 'border-slate-200 hover:border-purple-200 bg-white' }}">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xl">🟣</span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $activeNetwork === 'usdt_polygon' ? 'bg-[#673DE6] text-white' : 'bg-slate-100 text-slate-600' }}" id="badge-usdt_polygon">
                                        Polygon
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-extrabold text-slate-900">USDT (Polygon)</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Ultra-low gas &lt;$0.01</div>
                                </div>
                            </button>

                        </div>

                        <!-- Amount & QR Station Showcase Container -->
                        <div class="p-6 bg-slate-50/90 border border-slate-200/90 rounded-2xl">
                            
                            <!-- Amount Due Box -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-white rounded-xl border border-slate-200/80 mb-6 shadow-soft-sm">
                                <div>
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Exact Amount to Send</span>
                                    <div class="text-2xl sm:text-3xl font-black text-slate-900 flex items-baseline gap-2">
                                        <span>{{ $totalAmount }}</span>
                                        <span class="text-base font-bold text-[#673DE6]" id="active-coin-symbol">USDT</span>
                                    </div>
                                    <span class="text-xs text-slate-500">1:1 USD Rate ($ {{ $totalAmount }} USD)</span>
                                </div>
                                <button type="button" 
                                        onclick="copyToClipboard('{{ $totalAmount }}', 'btn-copy-amount')"
                                        id="btn-copy-amount"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-[#673DE6] text-xs font-bold transition-all border border-purple-200 shrink-0 self-start sm:self-auto cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                    <span>Copy Amount</span>
                                </button>
                            </div>

                            <!-- QR Code & Address Display Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                                
                                <!-- QR Code Box (4 Cols) -->
                                <div class="md:col-span-4 flex flex-col items-center justify-center p-4 bg-white rounded-2xl border border-slate-200 shadow-soft-sm text-center">
                                    <div class="p-2 bg-white rounded-xl border border-slate-100 mb-2">
                                        <img id="qr-code-img" 
                                             src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&margin=8&data={{ $trc20Address }}" 
                                             alt="Crypto Wallet QR Code" 
                                             class="w-40 h-40 object-contain rounded-lg">
                                    </div>
                                    <span class="text-[11px] text-slate-500 font-semibold flex items-center gap-1">
                                        <span>📷</span> Scan with wallet app
                                    </span>
                                </div>

                                <!-- Address & Warning Details (8 Cols) -->
                                <div class="md:col-span-8 space-y-4">
                                    
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-700" id="label-wallet-address">
                                                Deposit Wallet Address (Tron TRC-20):
                                            </span>
                                            <a id="link-explorer" 
                                               href="https://tronscan.org/#/address/{{ $trc20Address }}" 
                                               target="_blank" 
                                               class="text-[11px] font-bold text-[#673DE6] hover:underline flex items-center gap-1">
                                                <span>View on Explorer</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        </div>

                                        <!-- Copyable Address Container -->
                                        <div class="p-3.5 bg-white border-2 border-slate-200 rounded-xl flex items-center justify-between gap-2 shadow-soft-sm">
                                            <span class="font-mono text-xs sm:text-sm font-extrabold text-slate-900 break-all select-all" id="text-wallet-address">
                                                {{ $trc20Address }}
                                            </span>
                                            <button type="button" 
                                                    onclick="copyActiveAddress()" 
                                                    id="btn-copy-address" 
                                                    class="btn-shimmer bg-[#673DE6] hover:bg-[#5428D8] text-white font-bold px-3.5 py-2 rounded-lg text-xs transition-all shrink-0 flex items-center gap-1.5 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                                <span id="label-copy-address">Copy Address</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Network Warning Capsule -->
                                    <div class="p-3.5 rounded-xl bg-amber-50/80 border border-amber-200 text-amber-900 text-xs leading-relaxed flex items-start gap-2.5">
                                        <span class="text-base leading-none">⚠️</span>
                                        <div>
                                            <span class="font-bold block" id="warning-header">Network Precaution:</span>
                                            <span id="warning-text">Send only <strong>USDT via Tron Network (TRC-20)</strong>. Sending via any other network or sending the wrong currency will result in permanent loss.</span>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- CARD 2: SUBMIT BLOCKCHAIN PAYMENT PROOF (TxID) -->
                    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-soft-sm">
                        
                        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-[#673DE6] text-white font-black text-sm flex items-center justify-center shadow-md shadow-[#673DE6]/30">
                                    🔍
                                </span>
                                <div>
                                    <h2 class="text-lg font-extrabold text-slate-900">Step 2: Submit Transaction Proof</h2>
                                    <p class="text-xs text-slate-500">Provide the Transaction Hash (TxID) from your wallet to accelerate verification.</p>
                                </div>
                            </div>
                        </div>

                        @if($hasSubmittedTxid)
                            <!-- Already Submitted View -->
                            <div class="p-5 rounded-2xl bg-purple-50/80 border border-purple-200 space-y-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span class="text-xs font-extrabold uppercase tracking-wider text-purple-900">Payment Proof Submitted</span>
                                    </div>
                                    <span class="text-xs text-amber-700 bg-amber-100 border border-amber-200 font-bold px-3 py-1 rounded-full">
                                        ⏳ Verification in Progress
                                    </span>
                                </div>

                                <div>
                                    <div class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Submitted Transaction Hash (TxID):</div>
                                    <div class="p-3 bg-white rounded-xl border border-purple-200 font-mono text-xs sm:text-sm font-bold text-slate-900 break-all select-all flex items-center justify-between gap-2">
                                        <span>{{ $invoice->crypto_txid }}</span>
                                        @php
                                            $explorerUrl = str_starts_with($invoice->crypto_txid, '0x') || str_contains($invoice->crypto_network ?? '', 'polygon')
                                                ? "https://polygonscan.com/tx/{$invoice->crypto_txid}"
                                                : "https://tronscan.org/#/transaction/{$invoice->crypto_txid}";
                                        @endphp
                                        <a href="{{ $explorerUrl }}" target="_blank" class="text-xs font-bold text-[#673DE6] hover:underline shrink-0 flex items-center gap-1">
                                            <span>Explorer</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Our system administrators have received your transaction proof. Once confirmed on the blockchain, your VPS instance will deploy automatically. You can monitor your server status inside your client portal.
                                </p>

                                <div class="pt-2 flex flex-wrap gap-3">
                                    <a href="{{ url('/customer/invoices') }}" class="btn-shimmer bg-[#673DE6] hover:bg-[#5428D8] text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-md shadow-[#673DE6]/25 transition-all inline-flex items-center gap-1.5">
                                        <span>Go to Customer Invoices</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                    <button type="button" onclick="document.getElementById('txid-form-container').classList.toggle('hidden')" class="bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold px-4 py-2.5 rounded-xl border border-slate-200 transition-colors">
                                        Update Transaction Hash
                                    </button>
                                </div>
                            </div>
                        @endif

                        <!-- Form Container (Hidden if already submitted unless toggled) -->
                        <div id="txid-form-container" class="{{ $hasSubmittedTxid ? 'hidden mt-6 pt-6 border-t border-slate-100' : '' }}">
                            <form action="{{ route('checkout.crypto-submit-txid', $invoice->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="crypto_network" id="form-crypto-network" value="{{ $activeNetwork }}">

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                        Network Used For Transfer
                                    </label>
                                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-900 flex items-center justify-between" id="form-network-display">
                                        <span>USDT (Tron TRC-20)</span>
                                        <span class="text-xs text-purple-600 font-semibold cursor-pointer" onclick="document.querySelector('.network-tab-btn').focus()">Change network above ↑</span>
                                    </div>
                                </div>

                                <div>
                                    <label for="crypto_txid" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                        Transaction Hash / ID (TxID) <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="crypto_txid" 
                                           id="crypto_txid" 
                                           value="{{ old('crypto_txid', $invoice->crypto_txid ?? '') }}" 
                                           placeholder="e.g. 74d89e5a... or 0x4f8b2c..." 
                                           required 
                                           class="w-full bg-white border-2 border-slate-200 rounded-xl px-4 py-3 text-xs sm:text-sm font-mono text-slate-900 focus:ring-2 focus:ring-[#673DE6] focus:border-[#673DE6] outline-none transition-all">
                                    <p class="text-[11px] text-slate-500 mt-1">
                                        You can find this hash in your exchange withdrawal history (Binance, OKX, Bybit) or wallet activity (TrustWallet, TronLink, MetaMask).
                                    </p>
                                </div>

                                <button type="submit" class="btn-shimmer w-full bg-[#673DE6] hover:bg-[#5428D8] text-white font-extrabold py-3.5 px-6 rounded-xl shadow-xl shadow-[#673DE6]/30 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2 text-sm cursor-pointer">
                                    <span>🚀</span>
                                    <span>Confirm Payment & Submit TxID</span>
                                </button>
                            </form>
                        </div>

                    </div>

                </div>

                <!-- RIGHT COLUMN: Sticky Invoice & Order Summary (4-5 Cols) -->
                <div class="lg:col-span-5 xl:col-span-4 sticky top-28 space-y-6">
                    
                    <div class="rounded-3xl bg-white border border-slate-200 shadow-soft-lg overflow-hidden">
                        
                        <!-- Header Banner -->
                        <div class="p-6 bg-gradient-to-r from-[#180033] to-[#25004A] text-white">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-purple-300">Invoice Details</span>
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-400/30 text-[10px] font-bold font-mono">
                                    Awaiting Transfer
                                </span>
                            </div>
                            <h3 class="text-2xl font-black text-white tracking-tight">#{{ $invoice->invoice_number }}</h3>
                            <div class="text-xs text-purple-200 mt-0.5">Order Ref: {{ $order->order_number ?? 'ORD-PENDING' }}</div>
                        </div>

                        <!-- Hardware Specs Snapshot -->
                        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Provisioning Instance</div>
                            <ul class="space-y-2 text-xs sm:text-sm text-slate-700">
                                <li class="flex items-center justify-between">
                                    <span class="text-slate-500">Plan</span>
                                    <span class="font-extrabold text-slate-900">{{ $specsJson['package_name'] ?? 'Cloud VPS' }}</span>
                                </li>
                                <li class="flex items-center justify-between">
                                    <span class="text-slate-500">CPU Compute</span>
                                    <span class="font-bold text-slate-900">{{ $specsJson['cores'] ?? 'vCPU' }}</span>
                                </li>
                                <li class="flex items-center justify-between">
                                    <span class="text-slate-500">Memory (RAM)</span>
                                    <span class="font-bold text-slate-900">{{ $specsJson['memory'] ?? 'RAM' }}</span>
                                </li>
                                <li class="flex items-center justify-between">
                                    <span class="text-slate-500">Datacenter</span>
                                    <span class="font-bold text-slate-900">{{ $specsJson['datacenter'] ?? 'EU Central' }}</span>
                                </li>
                                <li class="flex items-center justify-between">
                                    <span class="text-slate-500">OS Image</span>
                                    <span class="font-bold text-slate-900 truncate max-w-[140px]">{{ $specsJson['os'] ?? 'Ubuntu' }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="p-6 space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-600">Subtotal</span>
                                <span class="font-bold text-slate-900">${{ number_format($invoice->amount, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-600">Provisioning Fee</span>
                                <span class="font-bold text-emerald-600 uppercase text-xs">FREE ($0.00)</span>
                            </div>
                            <div class="pt-3 border-t border-slate-100 flex items-baseline justify-between">
                                <div>
                                    <div class="text-base font-bold text-slate-900">Total Due:</div>
                                    <div class="text-xs text-slate-500">Fixed 1:1 Peg</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-3xl font-black text-[#673DE6] tracking-tight">
                                        ${{ $totalAmount }}
                                    </div>
                                    <span class="text-[11px] font-bold text-purple-600 block">{{ $totalAmount }} USDT / USDC</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action links -->
                        <div class="p-6 pt-0 space-y-2">
                            <a href="{{ url('/customer/invoices') }}" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold py-3 px-4 rounded-xl text-xs transition-colors flex items-center justify-center gap-1.5">
                                <span>View In Customer Portal</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>

                    </div>

                    <!-- Trust & Guarantee Capsule -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200 space-y-3 text-xs text-slate-600 shadow-soft-sm">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span><strong>No Middleman Gateway Fees</strong>: Direct on-chain deposit</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span><strong>Fast Activation</strong>: Provisioning starts upon confirmation</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span><strong>24/7 Sysadmin Support</strong> via Live Ticket desk</span>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- Interactive Network Switcher Script -->
    <script>
        const networksConfig = {
            'usdt_trc20': {
                name: 'USDT (Tron TRC-20)',
                symbol: 'USDT',
                badge: 'TRC-20',
                address: '{{ $trc20Address }}',
                explorer: 'https://tronscan.org/#/address/{{ $trc20Address }}',
                warningToken: 'USDT via Tron Network (TRC-20)',
                warningDetail: 'Send only <strong>USDT via Tron Network (TRC-20)</strong>. Sending via any other network or sending the wrong currency will result in permanent loss.'
            },
            'usdc_polygon': {
                name: 'USDC (Polygon PoS)',
                symbol: 'USDC',
                badge: 'Polygon',
                address: '{{ $usdcPolyAddress }}',
                explorer: 'https://polygonscan.com/address/{{ $usdcPolyAddress }}',
                warningToken: 'USDC via Polygon PoS Network',
                warningDetail: 'Send only <strong>USDC via Polygon PoS Network</strong>. Ensure your wallet/exchange network is selected as Polygon (MATIC).'
            },
            'usdt_polygon': {
                name: 'USDT (Polygon PoS)',
                symbol: 'USDT',
                badge: 'Polygon',
                address: '{{ $usdtPolyAddress }}',
                explorer: 'https://polygonscan.com/address/{{ $usdtPolyAddress }}',
                warningToken: 'USDT via Polygon PoS Network',
                warningDetail: 'Send only <strong>USDT via Polygon PoS Network</strong>. Ensure your wallet/exchange network is selected as Polygon (MATIC).'
            }
        };

        let currentActiveNetwork = '{{ $activeNetwork }}';

        function switchCryptoNetwork(networkKey) {
            const net = networksConfig[networkKey];
            if (!net) return;

            currentActiveNetwork = networkKey;

            // Update Tab active styling
            document.querySelectorAll('.network-tab-btn').forEach(btn => {
                btn.classList.remove('border-[#673DE6]', 'bg-purple-50/60', 'shadow-soft-sm');
                btn.classList.add('border-slate-200', 'bg-white');
            });
            const activeBtn = document.getElementById('btn-network-' + networkKey);
            if (activeBtn) {
                activeBtn.classList.remove('border-slate-200', 'bg-white');
                activeBtn.classList.add('border-[#673DE6]', 'bg-purple-50/60', 'shadow-soft-sm');
            }

            // Update badge colors
            ['usdt_trc20', 'usdc_polygon', 'usdt_polygon'].forEach(key => {
                const b = document.getElementById('badge-' + key);
                if (b) {
                    if (key === networkKey) {
                        b.className = 'text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-[#673DE6] text-white';
                    } else {
                        b.className = 'text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-slate-100 text-slate-600';
                    }
                }
            });

            // Update Coin Symbol
            document.getElementById('active-coin-symbol').textContent = net.symbol;

            // Update QR Code
            const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&margin=8&data=' + encodeURIComponent(net.address);
            document.getElementById('qr-code-img').src = qrUrl;

            // Update Wallet Address Text
            document.getElementById('text-wallet-address').textContent = net.address;
            document.getElementById('label-wallet-address').textContent = 'Deposit Wallet Address (' + net.name + '):';

            // Update Explorer link
            document.getElementById('link-explorer').href = net.explorer;

            // Update Warning text
            document.getElementById('warning-text').innerHTML = net.warningDetail;

            // Update hidden form input & display
            document.getElementById('form-crypto-network').value = networkKey;
            const formDisplay = document.getElementById('form-network-display');
            if (formDisplay) {
                formDisplay.innerHTML = '<span>' + net.name + '</span><span class="text-xs text-purple-600 font-semibold cursor-pointer" onclick="document.querySelector(\'.network-tab-btn\').focus()">Change network above ↑</span>';
            }
        }

        function copyActiveAddress() {
            const net = networksConfig[currentActiveNetwork];
            if (!net) return;
            copyToClipboard(net.address, 'btn-copy-address');
        }

        function copyToClipboard(text, buttonId) {
            navigator.clipboard.writeText(text).then(() => {
                const btn = document.getElementById(buttonId);
                if (!btn) return;
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<span>✓ Copied!</span>';
                btn.classList.add('bg-emerald-600');
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.classList.remove('bg-emerald-600');
                }, 2000);
            }).catch(err => {
                prompt('Copy this address:', text);
            });
        }
    </script>
</x-app-layout>
