<!-- Interactive Website Preview & Device Mode Switcher (Desktop / Tablet / Android Mobile Mode) -->
<div id="vortex-preview-system" class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-50 select-none font-sans">
    
    <!-- Collapsible Mini Floating Badge Button -->
    <button type="button" 
            id="vortex-preview-toggle-btn" 
            onclick="toggleVortexPreviewBar()"
            class="group flex items-center gap-2.5 px-4 py-2.5 rounded-full bg-[#120024] hover:bg-[#1E0038] text-white border border-purple-400/40 shadow-2xl shadow-purple-950/60 hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer"
            title="Toggle Device Preview (Desktop / Android Mode)"
    >
        <div class="relative flex h-2.5 w-2.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-purple-500"></span>
        </div>
        <span class="text-xs font-extrabold tracking-wide text-purple-200 group-hover:text-white flex items-center gap-1.5">
            <span>📱</span> <span>Preview & Modes</span>
        </span>
        <span id="vortex-preview-badge-res" class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-400/30">
            Auto
        </span>
    </button>

    <!-- Expanded Device Switcher & Quick Navigation Deck -->
    <div id="vortex-preview-panel" class="hidden absolute bottom-14 right-0 w-[340px] sm:w-[380px] bg-[#120024]/98 backdrop-blur-2xl border border-purple-500/40 rounded-3xl p-5 shadow-2xl shadow-purple-950/80 text-white space-y-4 animate-fade-in-up">
        
        <!-- Header with Live Viewport Resolution -->
        <div class="flex items-center justify-between pb-3 border-b border-white/10">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-xl bg-purple-600/30 border border-purple-400/30 flex items-center justify-center text-sm text-purple-300">⚙️</span>
                <div>
                    <h4 class="text-xs font-black uppercase tracking-wider text-white">Website Preview Mode</h4>
                    <p class="text-[11px] text-purple-300">ডেস্কটপ ও অ্যান্ড্রয়েড মোড প্রিভিউ</p>
                </div>
            </div>
            <button type="button" onclick="toggleVortexPreviewBar()" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- 1. Device Viewport Mode Switcher Buttons -->
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Device View Modes (ডিভাইস প্রিভিউ মোড):</label>
            <div class="grid grid-cols-3 gap-2">
                <!-- Desktop Mode -->
                <button type="button" 
                        onclick="setDevicePreviewMode('desktop')" 
                        id="btn-mode-desktop"
                        class="p-2.5 rounded-xl border border-white/15 bg-white/5 hover:bg-white/10 text-center flex flex-col items-center gap-1 transition-all"
                >
                    <span class="text-lg">🖥️</span>
                    <span class="text-[11px] font-bold text-white">Desktop</span>
                    <span class="text-[9px] text-slate-400 font-mono">100% Full</span>
                </button>

                <!-- Tablet Mode -->
                <button type="button" 
                        onclick="setDevicePreviewMode('tablet')" 
                        id="btn-mode-tablet"
                        class="p-2.5 rounded-xl border border-white/15 bg-white/5 hover:bg-white/10 text-center flex flex-col items-center gap-1 transition-all"
                >
                    <span class="text-lg">📟</span>
                    <span class="text-[11px] font-bold text-white">Tablet</span>
                    <span class="text-[9px] text-slate-400 font-mono">768px</span>
                </button>

                <!-- Android / Mobile Phone Mode -->
                <button type="button" 
                        onclick="setDevicePreviewMode('mobile')" 
                        id="btn-mode-mobile"
                        class="p-2.5 rounded-xl border border-white/15 bg-white/5 hover:bg-white/10 text-center flex flex-col items-center gap-1 transition-all"
                >
                    <span class="text-lg">📱</span>
                    <span class="text-[11px] font-bold text-white">Android</span>
                    <span class="text-[9px] text-purple-300 font-mono">390px</span>
                </button>
            </div>
        </div>

        <!-- 2. Live Viewport Dimensions Display -->
        <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/5 border border-white/10 text-xs">
            <span class="text-slate-400 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Current Viewport:
            </span>
            <span id="vortex-live-viewport-dim" class="font-mono font-bold text-purple-200">
                Window Width
            </span>
        </div>

        <!-- 3. One-Click Page Quick Switcher -->
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Instant Page Preview (পেজসমূহ):</label>
            <div class="grid grid-cols-2 gap-2 text-xs font-semibold">
                <a href="{{ url('/') }}" class="p-2 rounded-xl bg-white/5 hover:bg-purple-600/30 border border-white/10 hover:border-purple-400/40 text-left flex items-center gap-2 transition-all">
                    <span>🏠</span> <span>Home Page</span>
                </a>
                <a href="{{ url('/plans') }}" class="p-2 rounded-xl bg-white/5 hover:bg-purple-600/30 border border-white/10 hover:border-purple-400/40 text-left flex items-center gap-2 transition-all">
                    <span>⚡</span> <span>VPS Plans</span>
                </a>
                <a href="{{ url('/customer/login') }}" class="p-2 rounded-xl bg-white/5 hover:bg-purple-600/30 border border-white/10 hover:border-purple-400/40 text-left flex items-center gap-2 transition-all">
                    <span>👤</span> <span>Client Login</span>
                </a>
                <a href="{{ url('/admin') }}" target="_blank" class="p-2 rounded-xl bg-purple-600/20 hover:bg-purple-600/40 border border-purple-400/30 text-purple-200 text-left flex items-center gap-2 transition-all">
                    <span>🛡️</span> <span>Admin Panel</span>
                </a>
            </div>
        </div>

        <!-- 4. Helpline & Direct Call Button -->
        <div class="pt-2 border-t border-white/10 flex items-center justify-between text-xs">
            <span class="text-slate-400">24/7 হেল্পলাইন:</span>
            <a href="tel:01619789895" class="text-purple-300 hover:text-white font-mono font-bold flex items-center gap-1">
                <span>📞</span> <span>01619789895</span>
            </a>
        </div>

    </div>
</div>

<!-- JavaScript Engine for Device Preview & Viewport Management -->
<script>
    let currentDeviceMode = 'desktop';

    function toggleVortexPreviewBar() {
        const panel = document.getElementById('vortex-preview-panel');
        if (panel) {
            panel.classList.toggle('hidden');
        }
    }

    function updateLiveViewportDisplay() {
        const w = window.innerWidth;
        const h = window.innerHeight;
        const dimElem = document.getElementById('vortex-live-viewport-dim');
        const badgeElem = document.getElementById('vortex-preview-badge-res');
        
        let label = `${w} × ${h} px`;
        let short = `${w}px`;
        
        if (w < 640) {
            label += ' (Android)';
            short = '📱 Android';
        } else if (w < 1024) {
            label += ' (Tablet)';
            short = '📟 Tablet';
        } else {
            label += ' (Desktop)';
            short = '🖥️ Desktop';
        }

        if (dimElem) dimElem.textContent = label;
        if (badgeElem && currentDeviceMode === 'desktop') badgeElem.textContent = short;
    }

    function setDevicePreviewMode(mode) {
        currentDeviceMode = mode;
        const body = document.body;
        const desktopBtn = document.getElementById('btn-mode-desktop');
        const tabletBtn = document.getElementById('btn-mode-tablet');
        const mobileBtn = document.getElementById('btn-mode-mobile');
        const badgeElem = document.getElementById('vortex-preview-badge-res');

        // Reset button states
        [desktopBtn, tabletBtn, mobileBtn].forEach(b => {
            if (b) {
                b.classList.remove('bg-purple-600/40', 'border-purple-400', 'shadow-md');
                b.classList.add('bg-white/5', 'border-white/15');
            }
        });

        // Clear existing preview wrapper if any
        const existingFrame = document.getElementById('vortex-device-container-wrapper');
        if (existingFrame) {
            existingFrame.remove();
        }

        if (mode === 'desktop') {
            body.style.maxWidth = '';
            body.style.margin = '';
            body.style.boxShadow = '';
            body.style.borderRadius = '';
            if (desktopBtn) {
                desktopBtn.classList.add('bg-purple-600/40', 'border-purple-400', 'shadow-md');
                desktopBtn.classList.remove('bg-white/5', 'border-white/15');
            }
            if (badgeElem) badgeElem.textContent = '🖥️ Desktop';
        } else if (mode === 'tablet') {
            body.style.maxWidth = '768px';
            body.style.margin = '0 auto';
            body.style.boxShadow = '0 0 60px rgba(0,0,0,0.5)';
            body.style.borderRadius = '24px';
            if (tabletBtn) {
                tabletBtn.classList.add('bg-purple-600/40', 'border-purple-400', 'shadow-md');
                tabletBtn.classList.remove('bg-white/5', 'border-white/15');
            }
            if (badgeElem) badgeElem.textContent = '📟 768px Tablet';
        } else if (mode === 'mobile') {
            body.style.maxWidth = '412px';
            body.style.margin = '0 auto';
            body.style.boxShadow = '0 0 60px rgba(103,61,230,0.4)';
            body.style.borderRadius = '32px';
            if (mobileBtn) {
                mobileBtn.classList.add('bg-purple-600/40', 'border-purple-400', 'shadow-md');
                mobileBtn.classList.remove('bg-white/5', 'border-white/15');
            }
            if (badgeElem) badgeElem.textContent = '📱 Android';
        }

        // Trigger resize event so layouts dynamically reflow smoothly
        window.dispatchEvent(new Event('resize'));
    }

    window.addEventListener('resize', updateLiveViewportDisplay);
    document.addEventListener('DOMContentLoaded', () => {
        updateLiveViewportDisplay();
    });
</script>
