@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'robots' => 'index, follow',
    'canonical' => null,
    'ogImage' => null,
    'schema' => null,
    'headerVariant' => 'hero', // 'hero', 'solid', or 'minimal'
    'hideHeader' => false,
    'hideFooter' => false,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <!-- Modular SEO & OpenGraph Meta Component -->
    <x-seo-meta 
        :title="$title" 
        :description="$description" 
        :keywords="$keywords" 
        :robots="$robots" 
        :canonical="$canonical" 
        :ogImage="$ogImage" 
        :schema="$schema ?? null" 
    />
    
    <!-- Environment-Safe Analytics Tracking -->
    <x-analytics />

    <!-- Google Fonts: Inter & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles & Asset Bundling -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Global Rich Animation & Transition Styles (Zero-Library Luxury System) -->
    <style>
        /* Dynamic Announcement Bar Transition */
        #top-announcement-bar {
            transition: margin-top 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease;
        }

        /* Bespoke Luxury Cosmic Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0B0014;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(103, 61, 230, 0.35);
            border-radius: 9999px;
            border: 2px solid #0B0014;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(103, 61, 230, 0.7);
        }

        /* Smooth Entrance Keyframes */
        @keyframes pageFadeIn {
            from { opacity: 0; transform: translateY(16px) scale(0.99); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .animate-fade-in-up {
            animation: pageFadeIn 0.85s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Cinema-Grade Scroll-Reveal Base & Stagger Classes */
        .reveal-init {
            opacity: 0;
            transform: translateY(32px) scale(0.985);
            transition: opacity 0.85s cubic-bezier(0.16, 1, 0.3, 1), transform 0.85s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }

        .reveal-visible {
            opacity: 1 !important;
            transform: translateY(0) scale(1) !important;
        }

        .delay-75  { transition-delay: 75ms; }
        .delay-100 { transition-delay: 100ms; }
        .delay-150 { transition-delay: 150ms; }
        .delay-200 { transition-delay: 200ms; }
        .delay-250 { transition-delay: 250ms; }
        .delay-300 { transition-delay: 300ms; }
        .delay-400 { transition-delay: 400ms; }

        /* Button Refined Angled Shimmer & Kinetic Press */
        .btn-shimmer {
            position: relative;
            overflow: hidden;
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), 
                        box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-shimmer::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 200%;
            height: 100%;
            background: linear-gradient(115deg, transparent 20%, rgba(255,255,255,0.28) 50%, transparent 80%);
            transform: translateX(-150%);
            transition: transform 0.85s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: none;
        }
        .btn-shimmer:hover::after {
            transform: translateX(100%);
        }
        .btn-shimmer:active {
            transform: scale(0.98);
        }

        /* Springy Luxury Card Hover Dynamics with Cursor Spotlight */
        .card-interactive {
            position: relative;
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), 
                        box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1), 
                        border-color 0.25s ease;
            will-change: transform, box-shadow;
        }
        .card-interactive:hover {
            transform: translateY(-5px) scale(1.006);
            box-shadow: 0 22px 40px -15px rgba(103, 61, 230, 0.12), 0 0 0 1px rgba(103, 61, 230, 0.25);
        }

        /* Subtle Internal Radial Cursor Spotlight Sheen */
        .card-interactive::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: radial-gradient(400px circle at var(--mouse-x, -500px) var(--mouse-y, -500px), rgba(103, 61, 230, 0.05), transparent 75%);
            pointer-events: none;
            z-index: 1;
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        .card-interactive:hover::before {
            opacity: 1;
        }

        /* Smooth FAQ Accordion Expansion */
        details summary {
            transition: color 0.2s ease;
        }
        details[open] summary svg {
            transform: rotate(180deg);
        }

        /* Hide Horizontal Scrollbars on Floating Sub-Nav Pill */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <!-- Page-Specific Styles Stack -->
    @stack('styles')
</head>
<body class="bg-white text-slate-600 font-sans antialiased selection:bg-[#673DE6] selection:text-white flex flex-col min-h-screen">

    <!-- Global Toast & Flash Message Notification System -->
    <x-flash-messages />

    <!-- Modular Navigation Header -->
    @if(!$hideHeader)
        <x-header :variant="$headerVariant" />
    @endif

    <!-- Main Content Area with Adaptive Top Offset for Non-Hero Pages and Mobile Safe Bottom Spacing -->
    <main class="{{ $headerVariant !== 'hero' && !$hideHeader ? 'pt-28 sm:pt-32' : '' }} pb-16 lg:pb-0 flex-grow">
        {{ $slot }}
    </main>

    <!-- Modular Global Footer -->
    @if(!$hideFooter)
        <x-footer />
    @endif

    <!-- Android & iOS Mobile Bottom Action / Navigation Bar -->
    <x-mobile-bottom-nav />

    <!-- Interactive Website Preview & Device Mode Switcher (Desktop & Android Mode) -->
    <x-responsive-preview-bar />

    <!-- Global Navigation Scroll Controller & Scroll-Reveal IntersectionObserver -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Navigation & Announcement Bar Scroll Controller
            const header = document.getElementById('main-nav-header');
            const glassHighlight = document.getElementById('header-glass-highlight');
            const announcementBar = document.getElementById('top-announcement-bar');

            const handleHeaderScroll = () => {
                const scrollY = window.scrollY;

                // Announcement bar scrolls away with page (tucks away when scrolling down)
                if (announcementBar) {
                    if (scrollY > 25) {
                        announcementBar.classList.add('-mt-9', 'opacity-0', 'pointer-events-none');
                        announcementBar.classList.remove('mt-0', 'opacity-100');
                    } else {
                        announcementBar.classList.remove('-mt-9', 'opacity-0', 'pointer-events-none');
                        announcementBar.classList.add('mt-0', 'opacity-100');
                    }
                }

                // Frosted glass transition for hero header
                if (header) {
                    const variant = header.getAttribute('data-variant') || 'hero';
                    if (variant === 'hero') {
                        if (scrollY > 50) {
                            header.classList.remove('bg-transparent', 'border-transparent', 'shadow-none');
                            header.classList.add('backdrop-blur-2xl', 'bg-[#0F0024]/90', 'border-white/[0.12]', 'shadow-2xl', 'shadow-purple-950/40');
                            if (glassHighlight) glassHighlight.classList.remove('opacity-0');
                        } else {
                            header.classList.add('bg-transparent', 'border-transparent', 'shadow-none');
                            header.classList.remove('backdrop-blur-2xl', 'bg-[#0F0024]/90', 'border-white/[0.12]', 'shadow-2xl', 'shadow-purple-950/40');
                            if (glassHighlight) glassHighlight.classList.add('opacity-0');
                        }
                    }
                }
            };

            window.addEventListener('scroll', handleHeaderScroll, { passive: true });
            handleHeaderScroll();

            // 2. Mobile Menu Toggle
            const menuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            const openIcon = document.getElementById('menu-icon-open');
            const closeIcon = document.getElementById('menu-icon-close');

            if (menuBtn && mobileMenu) {
                menuBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                    if (openIcon) openIcon.classList.toggle('hidden');
                    if (closeIcon) closeIcon.classList.toggle('hidden');
                });

                mobileMenu.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        mobileMenu.classList.add('hidden');
                        if (openIcon) openIcon.classList.remove('hidden');
                        if (closeIcon) closeIcon.classList.add('hidden');
                    });
                });
            }

            // 3. High-Performance GPU Scroll Reveal Observer
            if ('IntersectionObserver' in window) {
                const revealObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('reveal-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.08,
                    rootMargin: '0px 0px -40px 0px'
                });

                document.querySelectorAll('.reveal-init').forEach(el => {
                    revealObserver.observe(el);
                });
            } else {
                // Fallback for older browsers
                document.querySelectorAll('.reveal-init').forEach(el => {
                    el.classList.add('reveal-visible');
                });
            }

            // 4. Subtle Radial Spotlight Tracking on Interactive Cards
            const interactiveCards = document.querySelectorAll('.card-interactive');
            if (interactiveCards.length && window.matchMedia('(pointer: fine)').matches) {
                interactiveCards.forEach(card => {
                    card.addEventListener('mousemove', e => {
                        const rect = card.getBoundingClientRect();
                        card.style.setProperty('--mouse-x', `${e.clientX - rect.left}px`);
                        card.style.setProperty('--mouse-y', `${e.clientY - rect.top}px`);
                    }, { passive: true });
                });
            }
        });
    </script>

    <!-- Free Vanilla CookieConsent v3 System -->
    <x-cookie-consent />

    <!-- Page-Specific Scripts Stack -->
    @stack('scripts')
</body>
</html>
