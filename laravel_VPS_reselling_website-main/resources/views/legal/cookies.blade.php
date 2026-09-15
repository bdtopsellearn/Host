<x-app-layout 
    title="Cookie Policy" 
    description="VortexCloud Cookie Policy. Understand how we use cookies, session tokens, and analytics trackers to power our cloud hosting platform."
    keywords="cookie policy, tracking cookies, gdpr cookie consent, browser cookies, analytics consent"
    headerVariant="hero"
    robots="index, follow">

    <!-- Page Header Stage -->
    <div class="bg-gradient-to-b from-[#120024] via-[#16002C] to-[#120024] text-white pt-32 pb-16 sm:pt-40 sm:pb-24 border-b border-white/10 relative overflow-hidden">
        <!-- Floating Ambient Stage Glows -->
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-purple-600/20 rounded-full blur-3xl pointer-events-none animate-float-slow"></div>
        <div class="absolute bottom-10 right-1/4 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none animate-float-reverse"></div>

        <div class="w-full max-w-[1680px] mx-auto px-4 sm:px-8 lg:px-12 xl:px-16 relative z-10">
            <div class="max-w-4xl mx-auto text-center space-y-4">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-purple-500/10 border border-purple-400/20 text-purple-300 text-xs font-semibold uppercase tracking-wider">
                    <svg class="w-4 h-4 text-purple-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5"></path>
                        <path d="M8.5 8.5v.01"></path>
                        <path d="M11.5 15.5v.01"></path>
                        <path d="M7.5 13.5v.01"></path>
                    </svg>
                    <span>Cookie Transparency & Consent</span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white">
                    Cookie Policy
                </h1>
                <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">
                    Learn about the cookies and browser storage technologies we utilize to authenticate sessions, secure orders, and analyze platform performance.
                </p>
                <div class="pt-3 flex flex-wrap items-center justify-center gap-4">
                    <button type="button" onclick="if(window.CookieConsent) { window.CookieConsent.showPreferences(); }" class="btn-shimmer bg-[#673DE6] hover:bg-[#5428D8] text-white font-bold text-xs sm:text-sm px-5 py-2.5 rounded-xl shadow-lg shadow-[#673DE6]/25 transition-all inline-flex items-center gap-2">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                        <span>Change Cookie Preferences</span>
                    </button>
                    <span class="text-xs text-slate-400 font-mono">Last Updated: {{ now()->startOfMonth()->format('F d, Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Container with Table of Contents -->
    <div class="bg-slate-50 py-12 sm:py-20">
        <div class="w-full max-w-[1680px] mx-auto px-4 sm:px-8 lg:px-12 xl:px-16">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

                <!-- Sticky Table of Contents (Desktop Sidebar) -->
                <aside class="hidden lg:block lg:col-span-3">
                    <div class="sticky top-36 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                        <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#673DE6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            Contents
                        </h2>
                        <nav class="space-y-2 text-sm">
                            <a href="#section-1" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">1. What Are Cookies?</a>
                            <a href="#section-2" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">2. Strictly Necessary Cookies</a>
                            <a href="#section-3" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">3. Analytics & Performance</a>
                            <a href="#section-4" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">4. Marketing & Pixels</a>
                            <a href="#section-5" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">5. Managing Preferences</a>
                        </nav>

                        <div class="pt-4 border-t border-slate-100">
                            <button type="button" onclick="if(window.CookieConsent) { window.CookieConsent.showPreferences(); }" class="w-full text-center py-2.5 px-3 bg-purple-50 hover:bg-purple-100 text-[#673DE6] rounded-xl text-xs font-bold transition-all border border-purple-200">
                                Open Cookie Settings
                            </button>
                        </div>
                    </div>
                </aside>

                <!-- Document Body -->
                <main class="lg:col-span-9 space-y-8">
                    
                    <!-- Card 1: What Are Cookies -->
                    <section id="section-1" class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-4">
                        <span class="text-xs font-bold text-[#673DE6] tracking-wider uppercase">Section 1</span>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">What Are Cookies and Tracking Technologies?</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            Cookies are miniature cryptographic text tokens and files stored directly inside your web browser directory when you navigate to <strong>VortexCloud</strong>. They permit our server infrastructure to remember your authenticated session across page transitions, secure checkout configurations, and respect your privacy choices.
                        </p>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            In accordance with European Union Directive 2002/58/EC (ePrivacy Directive) and the General Data Protection Regulation (GDPR), we do not load non-essential tracking cookies until you provide clear, affirmative consent via our cookie notification modal.
                        </p>
                    </section>

                    <!-- Card 2: Strictly Necessary Cookies -->
                    <section id="section-2" class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-5">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <span class="text-xs font-bold text-[#673DE6] tracking-wider uppercase">Section 2</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Always Active
                            </span>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Strictly Necessary & Security Cookies</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            These essential cookies ensure the core stability, customer portal authentication, and checkout security of VortexCloud. Because our infrastructure cannot operate safely without them, they are permanently enabled.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                                <div class="w-8 h-8 rounded-xl bg-purple-50 text-[#673DE6] flex items-center justify-center font-bold text-sm">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-slate-900 text-sm">Session Authentication</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Maintains your secure login state and configuration selections across our checkout steps and customer dashboard.
                                </p>
                            </div>

                            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                                <div class="w-8 h-8 rounded-xl bg-purple-50 text-[#673DE6] flex items-center justify-center font-bold text-sm">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-slate-900 text-sm">CSRF & Fraud Defense</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Generates unique cryptographic tokens to protect your forms and payment requests from unauthorized third-party forgery.
                                </p>
                            </div>

                            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                                <div class="w-8 h-8 rounded-xl bg-purple-50 text-[#673DE6] flex items-center justify-center font-bold text-sm">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-slate-900 text-sm">Consent Memory</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Remembers your privacy choices and cookie consent permissions so you are not interrupted repeatedly.
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Card 3: Analytics & Performance -->
                    <section id="section-3" class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-5">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <span class="text-xs font-bold text-[#673DE6] tracking-wider uppercase">Section 3</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-[#673DE6] border border-purple-200">
                                Optional &bull; Consent Required
                            </span>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Performance & Diagnostic Analytics</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            These optional cookies help us evaluate platform responsiveness, compute load, and network latency across global regions so we can refine our server deployment speeds.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                                <div class="w-8 h-8 rounded-xl bg-purple-50 text-[#673DE6] flex items-center justify-center font-bold text-sm">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-slate-900 text-sm">Aggregated Traffic & Speed Telemetry</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Monitors anonymous page load performance, datacenter latency benchmarks, and error rates via Google Analytics 4. Never tied to individual identities.
                                </p>
                            </div>

                            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                                <div class="w-8 h-8 rounded-xl bg-purple-50 text-[#673DE6] flex items-center justify-center font-bold text-sm">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-slate-900 text-sm">Diagnostic Optimization</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Detects broken navigation links and customer portal configuration bottlenecks to ensure seamless self-service management.
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Card 4: Marketing & Pixels -->
                    <section id="section-4" class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-5">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <span class="text-xs font-bold text-[#673DE6] tracking-wider uppercase">Section 4</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-[#673DE6] border border-purple-200">
                                Optional &bull; Consent Required
                            </span>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Campaign & Marketing Attribution</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            When permitted by you, marketing scripts (such as Meta Pixel) assist in measuring the efficacy of developer campaigns without collecting personally identifiable information. These trackers remain <strong>100% blocked by default</strong> until you grant explicit marketing consent in our preference center.
                        </p>
                    </section>

                    <!-- Card 5: Managing Preferences -->
                    <section id="section-5" class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-4">
                        <span class="text-xs font-bold text-[#673DE6] tracking-wider uppercase">Section 5</span>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">How to Control and Update Your Cookie Choices</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            You have total control over your cookie permissions. You may modify your consent categories at any time by clicking the button below or by using the permanent "Cookie Settings" link located in the footer of every page.
                        </p>
                        
                        <div class="pt-4 flex items-center gap-4">
                            <button type="button" onclick="if(window.CookieConsent) { window.CookieConsent.showPreferences(); }" class="btn-shimmer bg-[#673DE6] hover:bg-[#5428D8] text-white font-bold text-sm px-6 py-3 rounded-xl shadow-lg shadow-[#673DE6]/25 transition-all inline-flex items-center gap-2">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                                <span>Open Cookie Preference Center</span>
                            </button>
                        </div>
                    </section>

                </main>
            </div>
        </div>
    </div>

</x-app-layout>
