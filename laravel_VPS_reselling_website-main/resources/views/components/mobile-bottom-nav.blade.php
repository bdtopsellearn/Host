<!-- Android & iOS Mobile Bottom Action / Navigation Bar -->
<div id="mobile-bottom-bar" class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-[#0F0024]/95 backdrop-blur-xl border-t border-white/15 px-3 py-2 shadow-2xl transition-transform duration-300">
    <div class="max-w-md mx-auto grid grid-cols-5 items-center text-center text-[11px] font-medium text-slate-300">
        <!-- Home -->
        <a href="{{ url('/') }}" class="flex flex-col items-center py-1 rounded-xl transition-colors {{ request()->is('/') ? 'text-purple-400 font-bold' : 'hover:text-white' }}">
            <span class="text-lg">🏠</span>
            <span class="mt-0.5">হোম</span>
        </a>

        <!-- Plans -->
        <a href="{{ url('/plans') }}" class="flex flex-col items-center py-1 rounded-xl transition-colors {{ request()->is('plans*') ? 'text-purple-400 font-bold' : 'hover:text-white' }}">
            <span class="text-lg">⚡</span>
            <span class="mt-0.5">প্যাকেজ</span>
        </a>

        <!-- Instant Buy / CTA Center Button -->
        <a href="{{ url('/plans') }}" class="flex flex-col items-center -mt-4 group">
            <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-[#673DE6] to-[#A855F7] text-white flex items-center justify-center shadow-lg shadow-purple-600/50 group-hover:scale-105 active:scale-95 transition-all border-2 border-[#120024]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            </div>
            <span class="mt-0.5 text-[10px] font-extrabold text-purple-300">অর্ডার</span>
        </a>

        <!-- Client Portal -->
        @auth
            <a href="{{ url('/customer') }}" class="flex flex-col items-center py-1 rounded-xl transition-colors {{ request()->is('customer*') ? 'text-purple-400 font-bold' : 'hover:text-white' }}">
                <span class="text-lg">👤</span>
                <span class="mt-0.5">ড্যাশবোর্ড</span>
            </a>
        @else
            <a href="{{ url('/customer/login') }}" class="flex flex-col items-center py-1 rounded-xl transition-colors {{ request()->is('customer*') ? 'text-purple-400 font-bold' : 'hover:text-white' }}">
                <span class="text-lg">🔐</span>
                <span class="mt-0.5">লগইন</span>
            </a>
        @endauth

        <!-- Admin / Support Quick Link -->
        <a href="{{ url('/admin') }}" target="_blank" class="flex flex-col items-center py-1 rounded-xl transition-colors hover:text-white text-purple-300">
            <span class="text-lg">🛡️</span>
            <span class="mt-0.5">এডমিন</span>
        </a>
    </div>
</div>
