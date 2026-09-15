<x-app-layout 
    title="VPS Hosting Plans & Pricing | Enterprise NVMe Cloud" 
    description="Deploy high-performance NVMe cloud VPS instances powered by high-frequency multi-core compute, ECC memory, Gen4 RAID-10 storage, and instant automated provisioning."
    keywords="vps hosting, cloud vps, nvme vps, linux vps, windows rdp, kvm hosting, developer cloud, cheap vps, high frequency vps"
    headerVariant="hero"
>
    <x-slot:schema>
        <!-- Product & AggregateOffer Schema for Google Search Rich Snippets -->
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org/",
            "@@type": "Product",
            "name": "VortexCloud High-Frequency NVMe VPS Hosting",
            "image": "{{ url('/images/og-cover.png') }}",
            "description": "Enterprise cloud VPS hosting instances powered by high-frequency server processors, enterprise PCIe NVMe storage, and dedicated KVM virtualization.",
            "brand": {
                "@@type": "Brand",
                "name": "VortexCloud"
            },
            "offers": {
                "@@type": "AggregateOffer",
                "priceCurrency": "USD",
                "lowPrice": "4.99",
                "highPrice": "49.99",
                "offerCount": "5",
                "availability": "https://schema.org/InStock",
                "seller": {
                    "@@type": "Organization",
                    "name": "VortexCloud"
                }
            }
        }
        </script>

        <!-- FAQPage Schema for Structured Search Visibility -->
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "FAQPage",
            "mainEntity": [
                {
                    "@@type": "Question",
                    "name": "What is KVM VPS Hosting?",
                    "acceptedAnswer": {
                        "@@type": "Answer",
                        "text": "KVM (Kernel-based Virtual Machine) is a true hardware virtualization technology. Each VPS gets fully dedicated CPU cores, RAM, and storage with isolated kernel environments, ensuring 100% resource dedication with zero noisy neighbor interference."
                    }
                },
                {
                    "@@type": "Question",
                    "name": "How long does automated VPS provisioning take?",
                    "acceptedAnswer": {
                        "@@type": "Answer",
                        "text": "Deployment is 100% automated. As soon as your order is confirmed, your server configuration initializes, and your chosen operating system boots in under 60 seconds."
                    }
                },
                {
                    "@@type": "Question",
                    "name": "Do I get full root access and dedicated IP address?",
                    "acceptedAnswer": {
                        "@@type": "Answer",
                        "text": "Yes. Every VPS instance comes with unrestricted root / administrator access, a dedicated IPv4 address, and complete control to install custom software, configure firewalls, or upload custom ISOs."
                    }
                },
                {
                    "@@type": "Question",
                    "name": "Can I seamlessly upgrade my VPS resources later?",
                    "acceptedAnswer": {
                        "@@type": "Answer",
                        "text": "Yes. You can instantly scale your CPU cores, RAM, and NVMe disk space directly from your client control panel with zero data loss."
                    }
                },
                {
                    "@@type": "Question",
                    "name": "What operating systems and 1-click apps are supported?",
                    "acceptedAnswer": {
                        "@@type": "Answer",
                        "text": "We support all major Linux distributions including Ubuntu 24.04/22.04 LTS, Debian 12, AlmaLinux 9, Rocky Linux 9, as well as Windows Server (RDP edition) and 1-click developer stacks like Docker, CyberPanel, Node.js, and LAMP."
                    }
                }
            ]
        }
        </script>
    </x-slot:schema>

    @php
        $packages = \App\Models\Package::where('is_active', true)->orderBy('price_monthly')->get();
    @endphp

    <!-- SECTION 1: VPS HERO & VALUE PROPOSITION -->
    <section class="relative bg-gradient-to-b from-[#120024] via-[#16002C] to-[#120024] text-white pt-32 pb-20 md:pt-40 md:pb-28 overflow-hidden">
        <!-- Ambient Stage Lighting Glows -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-purple-600/20 rounded-full blur-3xl pointer-events-none animate-float-slow"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-fuchsia-600/20 rounded-full blur-3xl pointer-events-none animate-float-reverse"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="w-full max-w-[1680px] mx-auto px-4 sm:px-8 lg:px-12 xl:px-16 relative z-10">
            
            <div class="reveal-init text-center max-w-4xl mx-auto space-y-6">
                
                <!-- Category Capsule Pill -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-purple-200 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>High-Frequency Multi-Core & Gen4 NVMe Cloud</span>
                </div>

                <!-- Main H1 Headline -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-tight">
                    High-Performance <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-300 via-purple-200 to-indigo-200">KVM VPS Hosting</span>
                </h1>

                <!-- Sub-Headline -->
                <p class="text-base sm:text-lg lg:text-xl text-slate-300 leading-relaxed max-w-3xl mx-auto font-normal">
                    Enterprise virtual cloud servers engineered for speed, high-traffic APIs, database workloads, and full root-level control. Spin up in under 60 seconds.
                </p>

                <!-- Value Pillar Badges -->
                <div class="pt-4 flex flex-wrap items-center justify-center gap-4 sm:gap-6 text-xs sm:text-sm text-slate-300 font-medium">
                    <div class="flex items-center gap-2 bg-white/5 border border-white/10 px-3.5 py-2 rounded-xl backdrop-blur-sm">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>100% Dedicated KVM Resources</span>
                    </div>
                    <div class="flex items-center gap-2 bg-white/5 border border-white/10 px-3.5 py-2 rounded-xl backdrop-blur-sm">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Enterprise Gen4 NVMe Storage</span>
                    </div>
                    <div class="flex items-center gap-2 bg-white/5 border border-white/10 px-3.5 py-2 rounded-xl backdrop-blur-sm">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>2.4 Tbps Anti-DDoS Protection</span>
                    </div>
                    <div class="flex items-center gap-2 bg-white/5 border border-white/10 px-3.5 py-2 rounded-xl backdrop-blur-sm">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Unmetered Gigabit Network</span>
                    </div>
                </div>

            </div>

        </div>
    </section>


    <!-- SECTION 2: FLOATING DARK PILL SUB-MENU (HOSTINGER CAPSULE PATTERN) -->
    <div class="sticky top-20 z-30 py-3 flex justify-center pointer-events-none px-4">
        <nav id="floating-sub-nav" class="pointer-events-auto inline-flex items-center gap-1 sm:gap-1.5 p-1.5 rounded-full bg-[#16002C]/90 backdrop-blur-xl border border-white/15 shadow-2xl shadow-purple-950/70 overflow-x-auto max-w-full no-scrollbar">
            <a href="#plans" class="sub-nav-link px-4 sm:px-5 py-2 rounded-full text-xs sm:text-sm font-bold whitespace-nowrap transition-all duration-200 bg-white text-[#120024] shadow-md">
                Pricing
            </a>
            <a href="#specs-comparison" class="sub-nav-link px-3.5 sm:px-4 py-2 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap transition-all duration-200 text-slate-300 hover:text-white hover:bg-white/10">
                Specs Comparison
            </a>
            <a href="#comparison" class="sub-nav-link px-3.5 sm:px-4 py-2 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap transition-all duration-200 text-slate-300 hover:text-white hover:bg-white/10">
                vs Cloud Giants
            </a>
            <a href="#operating-systems" class="sub-nav-link px-3.5 sm:px-4 py-2 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap transition-all duration-200 text-slate-300 hover:text-white hover:bg-white/10">
                OS Templates
            </a>
            <a href="#datacenters" class="sub-nav-link px-3.5 sm:px-4 py-2 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap transition-all duration-200 text-slate-300 hover:text-white hover:bg-white/10">
                Locations
            </a>
            <a href="#features" class="sub-nav-link px-3.5 sm:px-4 py-2 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap transition-all duration-200 text-slate-300 hover:text-white hover:bg-white/10">
                Features
            </a>
            <a href="#faq" class="sub-nav-link px-3.5 sm:px-4 py-2 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap transition-all duration-200 text-slate-300 hover:text-white hover:bg-white/10">
                FAQ
            </a>
        </nav>
    </div>


    <!-- SECTION 3: PRICING MATRIX WITH INTERACTIVE BILLING SWITCHER -->
    <section id="plans" class="py-16 md:py-24 bg-slate-50/60 scroll-mt-24">
        <div class="w-full max-w-[1680px] mx-auto px-4 sm:px-8 lg:px-12 xl:px-16">
            
            <!-- Section Header & Billing Switcher -->
            <div class="reveal-init text-center max-w-3xl mx-auto mb-14">
                <div class="inline-block text-xs font-bold uppercase tracking-wider text-[#673DE6] bg-purple-50 px-3.5 py-1 rounded-full mb-3 border border-purple-200">
                    Transparent Cloud Pricing
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">
                    Choose Your Virtual Server Configuration
                </h2>
                <p class="text-base sm:text-lg text-slate-600 leading-relaxed mb-8">
                    Scale compute, memory, and high-speed NVMe storage on demand. 99.99% uptime SLA across all plans.
                </p>

            </div>

            <!-- Reusable Pricing Matrix (Switcher, Dynamic Cards & JS Logic Encapsulated) -->
            <x-pricing-matrix :packages="$packages" />

        </div>
    </section>


    <!-- SECTION 4: FULL TECHNICAL HARDWARE SPECS COMPARISON (HOSTINGER PATTERN) -->
    <section id="specs-comparison" class="py-16 md:py-24 bg-white scroll-mt-24 border-t border-slate-200">
        <div class="w-full max-w-[1680px] mx-auto px-4 sm:px-8 lg:px-12 xl:px-16">
            
            <div class="reveal-init text-center max-w-3xl mx-auto mb-16">
                <div class="inline-block text-xs font-bold uppercase tracking-wider text-[#673DE6] bg-purple-50 px-3.5 py-1 rounded-full mb-3 border border-purple-200">
                    Side-by-Side Specs
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">
                    Full Hardware & Architecture Comparison
                </h2>
                <p class="text-base sm:text-lg text-slate-600 leading-relaxed">
                    Compare computing power, storage throughput, networking, and platform capabilities across our entire Cloud VPS lineup.
                </p>
            </div>

            <!-- Comparison Table Container with Horizontal Scroll for Small Screens -->
            <div class="reveal-init overflow-x-auto rounded-3xl border border-slate-200 shadow-soft-sm bg-white">
                <table class="w-full text-left border-collapse min-w-[700px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="p-5 sm:p-6 text-sm font-bold text-slate-900 w-1/4">Hardware Specifications</th>
                            @foreach($packages->take(4) as $pkg)
                                <th class="p-5 sm:p-6 text-sm font-extrabold text-slate-900 text-center">
                                    <div class="text-base text-[#673DE6]">{{ trim($pkg->name) }}</div>
                                    <div class="text-xs text-slate-500 font-normal mt-0.5">${{ number_format($pkg->price_monthly, 2) }}/mo</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        
                        <!-- Compute Group -->
                        <tr class="bg-purple-50/40">
                            <td colspan="5" class="py-2.5 px-6 text-xs font-bold uppercase tracking-wider text-[#673DE6]">
                                Processor & Compute
                            </td>
                        </tr>
                        <tr>
                            <td class="p-5 font-semibold text-slate-900">vCPU Cores</td>
                            @foreach($packages->take(4) as $pkg)
                                @php $sp = is_string($pkg->specs) ? json_decode($pkg->specs, true) : (is_array($pkg->specs) ? $pkg->specs : []); @endphp
                                <td class="p-5 text-center font-bold text-slate-900">{{ $sp['cores'] ?? '4 vCPU Cores' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="p-5 font-semibold text-slate-900">CPU Architecture</td>
                            @foreach($packages->take(4) as $pkg)
                                <td class="p-5 text-center text-slate-600">High-Frequency Multi-Core CPU</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="p-5 font-semibold text-slate-900">Virtualization Technology</td>
                            @foreach($packages->take(4) as $pkg)
                                <td class="p-5 text-center text-slate-600">KVM (Hardware-Isolated)</td>
                            @endforeach
                        </tr>

                        <!-- Memory & Storage Group -->
                        <tr class="bg-purple-50/40">
                            <td colspan="5" class="py-2.5 px-6 text-xs font-bold uppercase tracking-wider text-[#673DE6]">
                                Memory & Storage
                            </td>
                        </tr>
                        <tr>
                            <td class="p-5 font-semibold text-slate-900">DDR5 ECC RAM</td>
                            @foreach($packages->take(4) as $pkg)
                                @php $sp = is_string($pkg->specs) ? json_decode($pkg->specs, true) : (is_array($pkg->specs) ? $pkg->specs : []); @endphp
                                <td class="p-5 text-center font-bold text-slate-900">{{ $sp['memory'] ?? '8 GB RAM' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="p-5 font-semibold text-slate-900">NVMe SSD Storage</td>
                            @foreach($packages->take(4) as $pkg)
                                @php $sp = is_string($pkg->specs) ? json_decode($pkg->specs, true) : (is_array($pkg->specs) ? $pkg->specs : []); @endphp
                                <td class="p-5 text-center font-bold text-slate-900">{{ $sp['storage'] ?? '100 GB SSD' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="p-5 font-semibold text-slate-900">Storage Architecture</td>
                            @foreach($packages->take(4) as $pkg)
                                <td class="p-5 text-center text-slate-600">Enterprise Gen4 NVMe RAID-10</td>
                            @endforeach
                        </tr>

                        <!-- Network & Security Group -->
                        <tr class="bg-purple-50/40">
                            <td colspan="5" class="py-2.5 px-6 text-xs font-bold uppercase tracking-wider text-[#673DE6]">
                                Network & Security
                            </td>
                        </tr>
                        <tr>
                            <td class="p-5 font-semibold text-slate-900">Network Port Speed</td>
                            @foreach($packages->take(4) as $pkg)
                                @php $sp = is_string($pkg->specs) ? json_decode($pkg->specs, true) : (is_array($pkg->specs) ? $pkg->specs : []); @endphp
                                <td class="p-5 text-center text-slate-900 font-semibold">{{ $sp['bandwidth'] ?? '200 Mbit/s Port' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="p-5 font-semibold text-slate-900">Monthly Traffic / Bandwidth</td>
                            @foreach($packages->take(4) as $pkg)
                                <td class="p-5 text-center text-emerald-600 font-bold">Unmetered (No Overages)</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="p-5 font-semibold text-slate-900">Dedicated IP Addresses</td>
                            @foreach($packages->take(4) as $pkg)
                                <td class="p-5 text-center text-slate-600">1 Dedicated IPv4 + /64 IPv6</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="p-5 font-semibold text-slate-900">Anti-DDoS Scrubbing</td>
                            @foreach($packages->take(4) as $pkg)
                                <td class="p-5 text-center text-slate-600">2.4 Tbps Multi-Layer Active</td>
                            @endforeach
                        </tr>

                        <!-- Management & Tools Group -->
                        <tr class="bg-purple-50/40">
                            <td colspan="5" class="py-2.5 px-6 text-xs font-bold uppercase tracking-wider text-[#673DE6]">
                                Control & Platform
                            </td>
                        </tr>
                        <tr>
                            <td class="p-5 font-semibold text-slate-900">Root / Admin Access</td>
                            @foreach($packages->take(4) as $pkg)
                                <td class="p-5 text-center text-emerald-600 font-bold">Full Root (SSH & VNC)</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="p-5 font-semibold text-slate-900">Automated Snapshots</td>
                            @foreach($packages->take(4) as $pkg)
                                <td class="p-5 text-center text-slate-900">Included (1-Click Restore)</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="p-5 font-semibold text-slate-900">1-Click OS Reinstallation</td>
                            @foreach($packages->take(4) as $pkg)
                                <td class="p-5 text-center text-emerald-600 font-bold">Supported</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="p-5 font-semibold text-slate-900">Action</td>
                            @foreach($packages->take(4) as $pkg)
                                <td class="p-5 text-center">
                                    <a href="{{ route('checkout.show', $pkg->id) }}" class="btn-shimmer inline-block bg-[#673DE6] hover:bg-[#5428D8] text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md transition-all">
                                        Deploy Now
                                    </a>
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </section>


    <!-- SECTION 5: TRANSPARENT RESOURCE ECONOMICS & VALUE SHOWCASE (OPTION 2) -->
    <x-competitor-comparison :packages="$packages" sectionId="comparison" />


    <!-- SECTION 6: 1-CLICK OPERATING SYSTEMS & APP STACKS CATALOG -->
    <section id="operating-systems" class="py-16 md:py-24 bg-slate-50/60 scroll-mt-24 border-t border-slate-200">
        <div class="w-full max-w-[1680px] mx-auto px-4 sm:px-8 lg:px-12 xl:px-16">
            
            <div class="reveal-init text-center max-w-3xl mx-auto mb-16">
                <div class="inline-block text-xs font-bold uppercase tracking-wider text-[#673DE6] bg-purple-50 px-3.5 py-1 rounded-full mb-3 border border-purple-200">
                    Instant OS & Tools
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">
                    Supported Operating Systems & 1-Click App Stacks
                </h2>
                <p class="text-base sm:text-lg text-slate-600 leading-relaxed">
                    Deploy your favorite Linux distribution, Windows Server edition, or pre-configured developer stack with a single click.
                </p>
            </div>

            <!-- OS Category Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Card 1: Linux Distributions -->
                <div class="reveal-init delay-100 bg-white rounded-3xl p-8 border border-slate-200 shadow-soft-sm hover:shadow-soft-md transition-all space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center font-bold text-xl">
                            🐧
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900">Linux Distributions</h3>
                            <p class="text-xs text-slate-500">Pure, clean ISOs updated monthly</p>
                        </div>
                    </div>
                    <ul class="space-y-3 text-sm text-slate-700 font-medium">
                        <li class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="font-semibold">Ubuntu</span>
                            <span class="text-xs text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200">24.04 / 22.04 LTS</span>
                        </li>
                        <li class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="font-semibold">Debian</span>
                            <span class="text-xs text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200">12 (Bookworm)</span>
                        </li>
                        <li class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="font-semibold">AlmaLinux</span>
                            <span class="text-xs text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200">9.4 / 8.9</span>
                        </li>
                        <li class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="font-semibold">Rocky Linux</span>
                            <span class="text-xs text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200">9.4</span>
                        </li>
                        <li class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="font-semibold">Alpine Linux</span>
                            <span class="text-xs text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200">3.20 (Minimal)</span>
                        </li>
                    </ul>
                </div>

                <!-- Card 2: Windows Server (RDP) -->
                <div class="reveal-init delay-200 bg-white rounded-3xl p-8 border border-slate-200 shadow-soft-sm hover:shadow-soft-md transition-all space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center font-bold text-xl">
                            🪟
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900">Windows Server RDP</h3>
                            <p class="text-xs text-slate-500">Dedicated GUI Remote Desktop</p>
                        </div>
                    </div>
                    <ul class="space-y-3 text-sm text-slate-700 font-medium">
                        <li class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="font-semibold">Windows Server 2022</span>
                            <span class="text-xs text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200">Datacenter 64-bit</span>
                        </li>
                        <li class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="font-semibold">Windows Server 2019</span>
                            <span class="text-xs text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200">Standard Edition</span>
                        </li>
                        <li class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="font-semibold">Remote Desktop (RDP)</span>
                            <span class="text-xs text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Pre-Configured</span>
                        </li>
                        <li class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="font-semibold">Direct Administrator</span>
                            <span class="text-xs text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Full Access</span>
                        </li>
                        <li class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="font-semibold">IIS & ASP.NET Core</span>
                            <span class="text-xs text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200">Ready</span>
                        </li>
                    </ul>
                </div>

                <!-- Card 3: 1-Click Developer Stacks & Panels -->
                <div class="reveal-init delay-300 bg-white rounded-3xl p-8 border border-slate-200 shadow-soft-sm hover:shadow-soft-md transition-all space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-purple-50 text-[#673DE6] border border-purple-200 flex items-center justify-center font-bold text-xl">
                            ⚡
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900">1-Click Stacks & Panels</h3>
                            <p class="text-xs text-slate-500">Automated pre-installed environments</p>
                        </div>
                    </div>
                    <ul class="space-y-3 text-sm text-slate-700 font-medium">
                        <li class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="font-semibold">Docker & Portainer</span>
                            <span class="text-xs text-purple-600 bg-purple-50 px-2 py-0.5 rounded border border-purple-200">Containers</span>
                        </li>
                        <li class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="font-semibold">CyberPanel / LiteSpeed</span>
                            <span class="text-xs text-purple-600 bg-purple-50 px-2 py-0.5 rounded border border-purple-200">Free Panel</span>
                        </li>
                        <li class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="font-semibold">cPanel & WHM</span>
                            <span class="text-xs text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200">Optional Addon</span>
                        </li>
                        <li class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="font-semibold">Node.js & PM2 Stack</span>
                            <span class="text-xs text-purple-600 bg-purple-50 px-2 py-0.5 rounded border border-purple-200">API Server</span>
                        </li>
                        <li class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="font-semibold">LAMP & NGINX Web Server</span>
                            <span class="text-xs text-purple-600 bg-purple-50 px-2 py-0.5 rounded border border-purple-200">Web Ready</span>
                        </li>
                    </ul>
                </div>

            </div>

        </div>
    </section>


    <!-- SECTION 6: GLOBAL LOW-LATENCY DATACENTER LOCATIONS -->
    <section id="datacenters" class="py-16 md:py-24 bg-white scroll-mt-24 border-t border-slate-200">
        <div class="w-full max-w-[1680px] mx-auto px-4 sm:px-8 lg:px-12 xl:px-16">
            
            <div class="reveal-init text-center max-w-3xl mx-auto mb-16">
                <div class="inline-block text-xs font-bold uppercase tracking-wider text-[#673DE6] bg-purple-50 px-3.5 py-1 rounded-full mb-3 border border-purple-200">
                    Low-Latency Global Edge
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">
                    Deploy Next to Your Global Audience
                </h2>
                <p class="text-base sm:text-lg text-slate-600 leading-relaxed">
                    Choose from carrier-grade Tier III+ datacenters across North America, Europe, and Asia-Pacific connected by unmetered 10Gbps uplinks.
                </p>
            </div>

            <!-- Datacenter Nodes Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Node 1: Frankfurt, Germany -->
                <div class="reveal-init delay-100 card-interactive bg-white rounded-2xl p-6 border border-slate-200 shadow-soft-sm hover:shadow-soft-md hover:border-purple-300 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span class="text-3xl">🇩🇪</span>
                        <div>
                            <h4 class="font-bold text-slate-900">Frankfurt, Germany</h4>
                            <p class="text-xs text-slate-500">Central European Exchange (DE-CIX)</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>12 ms</span>
                    </div>
                </div>

                <!-- Node 2: London, United Kingdom -->
                <div class="reveal-init delay-200 card-interactive bg-white rounded-2xl p-6 border border-slate-200 shadow-soft-sm hover:shadow-soft-md hover:border-purple-300 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span class="text-3xl">🇬🇧</span>
                        <div>
                            <h4 class="font-bold text-slate-900">London, United Kingdom</h4>
                            <p class="text-xs text-slate-500">LINX Direct Interconnect</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>14 ms</span>
                    </div>
                </div>

                <!-- Node 3: Ashburn, USA (East Coast) -->
                <div class="reveal-init delay-300 card-interactive bg-white rounded-2xl p-6 border border-slate-200 shadow-soft-sm hover:shadow-soft-md hover:border-purple-300 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span class="text-3xl">🇺🇸</span>
                        <div>
                            <h4 class="font-bold text-slate-900">Ashburn (VA), USA</h4>
                            <p class="text-xs text-slate-500">Data Center Alley Direct Peer</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>18 ms</span>
                    </div>
                </div>

                <!-- Node 4: Hillsboro / Portland, USA (West Coast) -->
                <div class="reveal-init delay-100 card-interactive bg-white rounded-2xl p-6 border border-slate-200 shadow-soft-sm hover:shadow-soft-md hover:border-purple-300 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span class="text-3xl">🇺🇸</span>
                        <div>
                            <h4 class="font-bold text-slate-900">Hillsboro (OR), USA</h4>
                            <p class="text-xs text-slate-500">West Coast Pacific Gateway</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>22 ms</span>
                    </div>
                </div>

                <!-- Node 5: Singapore (APAC) -->
                <div class="reveal-init delay-200 card-interactive bg-white rounded-2xl p-6 border border-slate-200 shadow-soft-sm hover:shadow-soft-md hover:border-purple-300 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span class="text-3xl">🇸🇬</span>
                        <div>
                            <h4 class="font-bold text-slate-900">Singapore (APAC)</h4>
                            <p class="text-xs text-slate-500">Equinix SG1 Asia-Pacific Hub</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>28 ms</span>
                    </div>
                </div>

                <!-- Node 6: Amsterdam, Netherlands -->
                <div class="reveal-init delay-300 card-interactive bg-white rounded-2xl p-6 border border-slate-200 shadow-soft-sm hover:shadow-soft-md hover:border-purple-300 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span class="text-3xl">🇳🇱</span>
                        <div>
                            <h4 class="font-bold text-slate-900">Amsterdam, Netherlands</h4>
                            <p class="text-xs text-slate-500">AMS-IX High-Bandwidth Transit</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>11 ms</span>
                    </div>
                </div>

            </div>

        </div>
    </section>


    <!-- SECTION 7: WHY CHOOSE VORTEXCLOUD (6-PILLAR INFRASTRUCTURE GRID) -->
    <section id="features" class="py-16 md:py-24 bg-slate-50/60 scroll-mt-24 border-t border-slate-200">
        <div class="w-full max-w-[1680px] mx-auto px-4 sm:px-8 lg:px-12 xl:px-16">
            
            <div class="reveal-init text-center max-w-3xl mx-auto mb-16">
                <div class="inline-block text-xs font-bold uppercase tracking-wider text-[#673DE6] bg-purple-50 px-3.5 py-1 rounded-full mb-3 border border-purple-200">
                    Enterprise Engineering
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">
                    Built for Developers. Scaled for Production.
                </h2>
                <p class="text-base sm:text-lg text-slate-600 leading-relaxed">
                    Zero resource sharing, pure enterprise NVMe performance, and modern cloud management built into every VPS instance.
                </p>
            </div>

            <!-- 6 Feature Pillars -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <!-- Feature 1 -->
                <div class="reveal-init delay-100 card-interactive bg-white rounded-3xl p-8 border border-slate-200 shadow-soft-sm hover:shadow-soft-md hover:border-purple-200 space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 border border-purple-100 flex items-center justify-center text-[#673DE6]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">Full Root & SSH Control</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Unrestricted root access with custom SSH key deployment, emergency web-based VNC console, and dedicated server-level environment configurations.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="reveal-init delay-200 card-interactive bg-white rounded-3xl p-8 border border-slate-200 shadow-soft-sm hover:shadow-soft-md hover:border-purple-200 space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 border border-purple-100 flex items-center justify-center text-[#673DE6]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">Dedicated KVM Virtualization</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Kernel-based virtual machines ensure strict resource isolation. Your RAM, vCPU compute threads, and storage bandwidth are never shared or overcommitted.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="reveal-init delay-300 card-interactive bg-white rounded-3xl p-8 border border-slate-200 shadow-soft-sm hover:shadow-soft-md hover:border-purple-200 space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 border border-purple-100 flex items-center justify-center text-[#673DE6]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">Enterprise Gen4 NVMe RAID-10</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Enterprise SSD arrays delivering over 7,200 MB/s read/write speeds and high random IOPS to power database queries and heavy concurrent workloads.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="reveal-init delay-100 card-interactive bg-white rounded-3xl p-8 border border-slate-200 shadow-soft-sm hover:shadow-soft-md hover:border-purple-200 space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 border border-purple-100 flex items-center justify-center text-[#673DE6]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">2.4 Tbps DDoS Protection</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Continuous multi-layer traffic inspection filters malicious volumetric attacks automatically without increasing latency or dropping legitimate traffic.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="reveal-init delay-200 card-interactive bg-white rounded-3xl p-8 border border-slate-200 shadow-soft-sm hover:shadow-soft-md hover:border-purple-200 space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 border border-purple-100 flex items-center justify-center text-[#673DE6]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">Automated Snapshots & Backups</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Create instant point-in-time snapshots before doing major software updates or roll back your virtual server with one click in seconds.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="reveal-init delay-300 card-interactive bg-white rounded-3xl p-8 border border-slate-200 shadow-soft-sm hover:shadow-soft-md hover:border-purple-200 space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 border border-purple-100 flex items-center justify-center text-[#673DE6]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">60-Second Instant Provisioning</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        No manual verification delays. Complete your payment and your cloud VPS dashboard credentials and IP addresses are ready immediately.
                    </p>
                </div>

            </div>

        </div>
    </section>


    <!-- SECTION 8: TECHNICAL VPS FAQ ACCORDION -->
    <section id="faq" class="py-16 md:py-24 bg-white scroll-mt-24 border-t border-slate-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="reveal-init text-center mb-14">
                <div class="inline-block text-xs font-bold uppercase tracking-wider text-[#673DE6] bg-purple-50 px-3.5 py-1 rounded-full mb-3 border border-purple-200">
                    Frequently Asked Questions
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">
                    Got Questions About Our VPS Hosting?
                </h2>
                <p class="text-base sm:text-lg text-slate-600 leading-relaxed">
                    Everything you need to know about resource scalability, root access, automated backups, and billing terms.
                </p>
            </div>

            <!-- FAQ Accordion List -->
            <div class="space-y-4">
                
                <!-- Q1 -->
                <details class="reveal-init delay-100 group bg-white rounded-2xl border border-slate-200 shadow-soft-sm p-6 [&_summary::-webkit-details-marker]:hidden open:border-purple-300 hover:border-purple-200 transition-all duration-200" open>
                    <summary class="flex items-center justify-between cursor-pointer font-bold text-slate-900 text-lg gap-4 group-hover:text-[#673DE6]">
                        <span>What is KVM VPS Hosting and how does it benefit me?</span>
                        <span class="w-8 h-8 rounded-xl bg-slate-50 group-open:bg-purple-50 text-slate-500 group-open:text-[#673DE6] flex items-center justify-center shrink-0 transition-transform duration-200 group-open:rotate-180">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </summary>
                    <div class="mt-4 pt-4 border-t border-slate-100 text-slate-600 leading-relaxed text-sm sm:text-base">
                        KVM (Kernel-based Virtual Machine) is true hardware-level virtualization. Unlike container-based hosting (like OpenVZ), KVM provides each VPS with isolated RAM, independent CPU cores, and an autonomous OS kernel. This guarantees your resources are 100% dedicated with zero noisy neighbor slowdowns.
                    </div>
                </details>

                <!-- Q2 -->
                <details class="reveal-init delay-200 group bg-white rounded-2xl border border-slate-200 shadow-soft-sm p-6 [&_summary::-webkit-details-marker]:hidden open:border-purple-300 hover:border-purple-200 transition-all duration-200">
                    <summary class="flex items-center justify-between cursor-pointer font-bold text-slate-900 text-lg gap-4 group-hover:text-[#673DE6]">
                        <span>How fast is server provisioning after placing an order?</span>
                        <span class="w-8 h-8 rounded-xl bg-slate-50 group-open:bg-purple-50 text-slate-500 group-open:text-[#673DE6] flex items-center justify-center shrink-0 transition-transform duration-200 group-open:rotate-180">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </summary>
                    <div class="mt-4 pt-4 border-t border-slate-100 text-slate-600 leading-relaxed text-sm sm:text-base">
                        Provisioning is fully automated. The moment your payment is processed (via Stripe Credit Card, Crypto, or Bank Transfer), our orchestration engine spins up your KVM instance, configures your network interfaces, and delivers your root credentials in under 60 seconds.
                    </div>
                </details>

                <!-- Q3 -->
                <details class="reveal-init delay-300 group bg-white rounded-2xl border border-slate-200 shadow-soft-sm p-6 [&_summary::-webkit-details-marker]:hidden open:border-purple-300 hover:border-purple-200 transition-all duration-200">
                    <summary class="flex items-center justify-between cursor-pointer font-bold text-slate-900 text-lg gap-4 group-hover:text-[#673DE6]">
                        <span>Can I upgrade my CPU, RAM, or storage later without data loss?</span>
                        <span class="w-8 h-8 rounded-xl bg-slate-50 group-open:bg-purple-50 text-slate-500 group-open:text-[#673DE6] flex items-center justify-center shrink-0 transition-transform duration-200 group-open:rotate-180">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </summary>
                    <div class="mt-4 pt-4 border-t border-slate-100 text-slate-600 leading-relaxed text-sm sm:text-base">
                        Yes. You can upgrade to a higher tier at any time from your customer portal. The upgrade applies seamlessly with an automated reboot, preserving all your files, database records, and server IP settings.
                    </div>
                </details>

                <!-- Q4 -->
                <details class="reveal-init delay-100 group bg-white rounded-2xl border border-slate-200 shadow-soft-sm p-6 [&_summary::-webkit-details-marker]:hidden open:border-purple-300 hover:border-purple-200 transition-all duration-200">
                    <summary class="flex items-center justify-between cursor-pointer font-bold text-slate-900 text-lg gap-4 group-hover:text-[#673DE6]">
                        <span>What happens if I exceed my bandwidth limit?</span>
                        <span class="w-8 h-8 rounded-xl bg-slate-50 group-open:bg-purple-50 text-slate-500 group-open:text-[#673DE6] flex items-center justify-center shrink-0 transition-transform duration-200 group-open:rotate-180">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </summary>
                    <div class="mt-4 pt-4 border-t border-slate-100 text-slate-600 leading-relaxed text-sm sm:text-base">
                        We never charge hidden overage fees. If you reach your monthly transfer quota, your connection is throttled to 10Mbps until the next cycle, or you can add bandwidth addons directly from your billing portal.
                    </div>
                </details>

                <!-- Q5 -->
                <details class="reveal-init delay-200 group bg-white rounded-2xl border border-slate-200 shadow-soft-sm p-6 [&_summary::-webkit-details-marker]:hidden open:border-purple-300 hover:border-purple-200 transition-all duration-200">
                    <summary class="flex items-center justify-between cursor-pointer font-bold text-slate-900 text-lg gap-4 group-hover:text-[#673DE6]">
                        <span>What is your uptime guarantee and SLA?</span>
                        <span class="w-8 h-8 rounded-xl bg-slate-50 group-open:bg-purple-50 text-slate-500 group-open:text-[#673DE6] flex items-center justify-center shrink-0 transition-transform duration-200 group-open:rotate-180">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </summary>
                    <div class="mt-4 pt-4 border-t border-slate-100 text-slate-600 leading-relaxed text-sm sm:text-base">
                        We provide an enterprise-grade 99.99% network uptime SLA across all high-frequency nodes. If an automated provisioning issue occurs on our platform, our 24/7 technical desk will immediately investigate or credit your account balance.
                    </div>
                </details>

            </div>

        </div>
    </section>


    <!-- SECTION 9: FINAL CONVERSION CTA (COSMIC VIOLET) -->
    <section class="py-16 md:py-24 bg-white border-t border-slate-200">
        <div class="w-full max-w-[1680px] mx-auto px-4 sm:px-8 lg:px-12 xl:px-16">
            
            <div class="reveal-init relative rounded-3xl bg-gradient-to-b from-[#1A0038] via-[#120024] to-[#220044] p-10 md:p-16 text-center border border-white/15 shadow-2xl shadow-purple-950/70 overflow-hidden">
                <!-- Ambient Cosmic Glows -->
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-purple-600/30 rounded-full blur-3xl pointer-events-none animate-float-slow"></div>
                <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-fuchsia-600/25 rounded-full blur-3xl pointer-events-none animate-float-reverse"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[350px] bg-indigo-600/15 rounded-full blur-[100px] pointer-events-none animate-pulse-glow"></div>

                <div class="relative z-10 max-w-3xl mx-auto space-y-6">
                    
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-purple-200 shadow-sm">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Enterprise Cloud Ready</span>
                    </div>

                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-tight">
                        Deploy Your High-Speed VPS in 60 Seconds
                    </h2>
                    
                    <p class="text-base sm:text-lg text-slate-300 leading-relaxed max-w-2xl mx-auto font-normal">
                        Experience low-latency Gen4 NVMe compute, unmetered network pipelines, and 24/7 developer-first support.
                    </p>

                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="#plans" class="btn-shimmer w-full sm:w-auto bg-white hover:bg-slate-100 text-[#120024] text-base sm:text-lg font-extrabold px-9 py-4 rounded-xl shadow-2xl hover:scale-105 active:scale-95 transition-all duration-200 inline-flex items-center justify-center gap-3">
                            <span>Explore Plans & Deploy</span>
                            <svg class="w-5 h-5 text-[#673DE6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>

                    <div class="pt-4 flex items-center justify-center gap-6 text-xs sm:text-sm text-slate-300 font-medium">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Instant automated setup
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Cancel anytime
                        </span>
                        <span class="hidden sm:flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            99.99% uptime guarantee
                        </span>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <script>
        // Sub-Navigation Scrollspy Active State Updater
        document.addEventListener('DOMContentLoaded', () => {
            const navLinks = document.querySelectorAll('#floating-sub-nav .sub-nav-link');
            const sections = [];
            
            navLinks.forEach(link => {
                const targetId = link.getAttribute('href');
                if (targetId && targetId.startsWith('#')) {
                    const section = document.querySelector(targetId);
                    if (section) sections.push({ link, section });
                }
            });

            // Sort sections by document position to guarantee strictly ordered scrollspy updates
            sections.sort((a, b) => a.section.offsetTop - b.section.offsetTop);

            function updateActiveSubNav() {
                const scrollY = window.scrollY + 220;
                let current = sections[0];

                for (let i = 0; i < sections.length; i++) {
                    if (scrollY >= sections[i].section.offsetTop) {
                        current = sections[i];
                    }
                }

                navLinks.forEach(link => {
                    link.className = 'sub-nav-link px-3.5 sm:px-4 py-2 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap transition-all duration-200 text-slate-300 hover:text-white hover:bg-white/10';
                });

                if (current && current.link) {
                    current.link.className = 'sub-nav-link px-4 sm:px-5 py-2 rounded-full text-xs sm:text-sm font-bold whitespace-nowrap transition-all duration-200 bg-white text-[#120024] shadow-md';
                }
            }

            window.addEventListener('scroll', updateActiveSubNav, { passive: true });
            updateActiveSubNav();
        });
    </script>
</x-app-layout>
