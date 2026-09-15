@if (session('success') || session('status') || session('error') || session('warning') || session('info'))
    <div 
        x-data="{ show: true }" 
        x-show="show" 
        x-init="setTimeout(() => show = false, 6000)" 
        id="global-toast-container"
        class="fixed top-24 right-4 sm:right-8 z-50 max-w-md w-full transition-all duration-300 transform ease-out"
        role="alert"
    >
        @if (session('success') || session('status'))
            <div class="flex items-center gap-3 bg-emerald-950/95 border border-emerald-500/40 text-emerald-100 p-4 rounded-2xl shadow-2xl backdrop-blur-xl ring-1 ring-emerald-500/20">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-sm shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="flex-grow text-xs sm:text-sm font-medium leading-snug">
                    {{ session('success') ?? session('status') }}
                </div>
                <button type="button" onclick="this.closest('#global-toast-container').remove()" class="text-emerald-400 hover:text-white p-1 rounded-lg focus:outline-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @elseif (session('error'))
            <div class="flex items-center gap-3 bg-rose-950/95 border border-rose-500/40 text-rose-100 p-4 rounded-2xl shadow-2xl backdrop-blur-xl ring-1 ring-rose-500/20">
                <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center font-bold text-sm shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div class="flex-grow text-xs sm:text-sm font-medium leading-snug">
                    {{ session('error') }}
                </div>
                <button type="button" onclick="this.closest('#global-toast-container').remove()" class="text-rose-400 hover:text-white p-1 rounded-lg focus:outline-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @elseif (session('warning'))
            <div class="flex items-center gap-3 bg-amber-950/95 border border-amber-500/40 text-amber-100 p-4 rounded-2xl shadow-2xl backdrop-blur-xl ring-1 ring-amber-500/20">
                <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-sm shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="flex-grow text-xs sm:text-sm font-medium leading-snug">
                    {{ session('warning') }}
                </div>
                <button type="button" onclick="this.closest('#global-toast-container').remove()" class="text-amber-400 hover:text-white p-1 rounded-lg focus:outline-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif
    </div>
@endif
