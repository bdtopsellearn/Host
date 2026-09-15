@props([
    'badge' => 'LIMITED PROMO',
    'message' => 'Save up to 20% on 24-Month Cloud VPS Plans with guaranteed fixed renewal rates.',
    'mobileMessage' => 'Save 20% on 2-Year Plans',
    'code' => 'LAUNCH20',
    'link' => url('/plans'),
    'linkText' => 'Claim Offer →',
])

<!-- Top Secondary Announcement & Promotional Banner (Scroll-Away) -->
<div 
    id="top-announcement-bar" 
    class="relative z-50 w-full bg-gradient-to-r from-[#120024] via-[#240049] to-[#120024] border-b border-purple-500/20 text-white h-9 overflow-hidden transition-[margin-top,opacity] duration-300 ease-in-out"
>
    <div class="w-full max-w-[1680px] mx-auto px-4 sm:px-8 lg:px-12 xl:px-16 h-full">
        <div class="flex items-center justify-center h-full text-xs">
            
            <!-- Message Container (Centered, No Close Button) -->
            <div class="flex items-center justify-center gap-2 sm:gap-3 text-center truncate">
                <!-- Glowing Offer Capsule Badge -->
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-gradient-to-r from-[#673DE6] to-purple-600 text-white shadow-sm shrink-0 border border-purple-400/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>{{ $badge }}</span>
                </span>

                <!-- Desktop Headline -->
                <span class="hidden md:inline text-purple-100 font-medium">
                    {{ $message }}
                </span>

                <!-- Mobile Condensed Headline -->
                <span class="inline md:hidden text-purple-100 font-medium truncate">
                    {{ $mobileMessage }}
                </span>

                <!-- Coupon Code Pill -->
                @if(!empty($code))
                    <span class="inline-flex items-center gap-1 font-mono text-[11px] font-bold text-purple-200 bg-white/10 border border-white/20 px-2 py-0.5 rounded shadow-inner shrink-0">
                        <span class="text-purple-300/80 font-sans text-[10px] font-normal hidden sm:inline">CODE:</span>
                        <span class="text-white">{{ $code }}</span>
                    </span>
                @endif

                <!-- Direct CTA Action -->
                @if(!empty($link))
                    <a href="{{ $link }}" class="inline-flex items-center text-xs font-bold text-white hover:text-purple-300 underline underline-offset-2 shrink-0 transition-colors">
                        {{ $linkText }}
                    </a>
                @endif
            </div>

        </div>
    </div>
</div>
