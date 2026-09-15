<!-- SaaS Clean Light-Mode Footer -->
<footer class="bg-white border-t border-slate-200">
    <div class="w-full max-w-[1680px] mx-auto px-4 sm:px-8 lg:px-12 xl:px-16 py-16">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-10">
            <!-- Brand Summary & Uptime -->
            <div class="md:col-span-2 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-500 flex items-center justify-center text-white shadow-md shadow-purple-600/20">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect>
                            <rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect>
                            <line x1="6" y1="6" x2="6.01" y2="6"></line>
                            <line x1="6" y1="18" x2="6.01" y2="18"></line>
                        </svg>
                    </div>
                    <span class="font-bold text-xl text-slate-900 tracking-tight">VortexCloud</span>
                </div>
                <p class="text-sm text-slate-600 max-w-sm leading-relaxed">
                    High-performance B2B virtual private servers powered by high-frequency multi-core compute and enterprise NVMe storage arrays with instant automated provisioning.
                </p>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-50 border border-slate-200 text-xs font-medium text-slate-700">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span>All Systems Operational (99.99% Uptime)</span>
                </div>
            </div>

            <!-- Products -->
            <div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-4">Compute & VPS</h3>
                <ul class="space-y-2.5 text-sm text-slate-600">
                    <li><a href="{{ url('/plans') }}" class="hover:text-[#673DE6] transition-colors">Starter NVMe VPS</a></li>
                    <li><a href="{{ url('/plans') }}" class="hover:text-[#673DE6] transition-colors">Professional VPS</a></li>
                    <li><a href="{{ url('/plans') }}" class="hover:text-[#673DE6] transition-colors">Enterprise VPS</a></li>
                    <li><a href="{{ url('/plans') }}" class="hover:text-[#673DE6] transition-colors">Windows RDP Servers</a></li>
                    <li><a href="{{ url('/plans') }}" class="hover:text-[#673DE6] transition-colors">Custom Reseller Tiers</a></li>
                </ul>
            </div>

            <!-- Infrastructure -->
            <div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-4">Infrastructure</h3>
                <ul class="space-y-2.5 text-sm text-slate-600">
                    <li><a href="{{ url('/plans') }}" class="hover:text-[#673DE6] transition-colors">Enterprise Cloud Nodes</a></li>
                    <li><a href="{{ url('/plans') }}" class="hover:text-[#673DE6] transition-colors">PCIe NVMe Storage</a></li>
                    <li><a href="{{ url('/#features') }}" class="hover:text-[#673DE6] transition-colors">DDoS Scrubbing Core</a></li>
                    <li><a href="{{ url('/#features') }}" class="hover:text-[#673DE6] transition-colors">Global Tier-1 Network</a></li>
                    <li><a href="{{ url('/#automation') }}" class="hover:text-[#673DE6] transition-colors">DevOps & Automation</a></li>
                </ul>
            </div>

            <!-- Support & Legal -->
            <div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-4">Support & Legal</h3>
                <ul class="space-y-2.5 text-sm text-slate-600">
                    <li><a href="{{ url('/customer/login') }}" class="hover:text-[#673DE6] transition-colors">Customer Portal</a></li>
                    <li><a href="{{ url('/#faq') }}" class="hover:text-[#673DE6] transition-colors">Knowledge Base & FAQ</a></li>
                    <li><a href="{{ url('/customer/support-tickets') }}" class="hover:text-[#673DE6] transition-colors">24/7 Expert Ticket Desk</a></li>
                    <li><a href="{{ route('legal.terms') }}" class="hover:text-[#673DE6] transition-colors">Terms of Service</a></li>
                    <li><a href="{{ route('legal.privacy') }}" class="hover:text-[#673DE6] transition-colors">Privacy Policy</a></li>
                    <li><a href="{{ route('legal.cookies') }}" class="hover:text-[#673DE6] transition-colors">Cookie Policy</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="mt-12 pt-8 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div class="flex flex-wrap items-center gap-4">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'VortexCloud') }} Technologies LLC. All rights reserved.</p>
                <span class="hidden sm:inline text-slate-300">&bull;</span>
                <a href="{{ route('legal.terms') }}" class="hover:text-[#673DE6] transition-colors">Terms</a>
                <a href="{{ route('legal.privacy') }}" class="hover:text-[#673DE6] transition-colors">Privacy</a>
                <a href="{{ route('legal.cookies') }}" class="hover:text-[#673DE6] transition-colors">Cookies</a>
                <button type="button" onclick="if(window.CookieConsent){window.CookieConsent.showPreferences();}" class="hover:text-[#673DE6] transition-colors underline cursor-pointer">
                    Cookie Preferences
                </button>
            </div>
            <div class="flex items-center space-x-6">
                <span class="text-slate-400">SOC 2 Type II Certified Datacenters</span>
                <span class="text-slate-400">1 Gbps - 10 Gbps Unmetered Uplinks</span>
            </div>
        </div>
    </div>
</footer>
