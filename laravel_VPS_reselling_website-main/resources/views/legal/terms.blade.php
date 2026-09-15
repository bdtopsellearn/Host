<x-app-layout 
    title="Terms of Service & Acceptable Use Policy" 
    description="VortexCloud master Terms of Service, Acceptable Use Policy (AUP), server governance rules, billing, and non-refundable digital service guidelines."
    keywords="terms of service, acceptable use policy, aup, vps terms, cloud hosting agreement, refund policy"
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
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    <span>Platform Terms & Server Governance</span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white">
                    Terms of Service
                </h1>
                <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">
                    Clear contractual terms governing the provisioning, acceptable usage, infrastructure integrity, and billing of all VortexCloud services.
                </p>
                <p class="text-xs text-slate-400 pt-2 font-mono">
                    Last Updated: {{ now()->startOfMonth()->format('F d, Y') }} &bull; Effective Immediately
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Container with Table of Contents -->
    <div class="bg-slate-50 py-12 sm:py-20">
        <div class="w-full max-w-[1680px] mx-auto px-4 sm:px-8 lg:px-12 xl:px-16">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

                <!-- Sticky Table of Contents (Desktop Sidebar) -->
                <aside class="hidden lg:block lg:col-span-3">
                    <div class="sticky top-36 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                        <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#673DE6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            Contents
                        </h2>
                        <nav class="space-y-2 text-sm">
                            <a href="#section-1" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">1. Acceptance & Eligibility</a>
                            <a href="#section-2" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">2. Acceptable Use Policy (AUP)</a>
                            <a href="#section-3" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">3. Non-Refund & Billing Terms</a>
                            <a href="#section-4" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">4. Server Provisioning & Access</a>
                            <a href="#section-5" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">5. SLA & Network Uptime</a>
                            <a href="#section-6" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">6. Suspension & Termination</a>
                            <a href="#section-7" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">7. Limitation of Liability</a>
                            <a href="#section-8" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">8. Governing Law & Contact</a>
                        </nav>
                    </div>
                </aside>

                <!-- Document Body -->
                <main class="lg:col-span-9 space-y-8">
                    
                    <!-- Card 1: Acceptance -->
                    <section id="section-1" class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-4">
                        <span class="text-xs font-bold text-[#673DE6] tracking-wider uppercase">Section 1</span>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Contractual Agreement & Account Eligibility</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            These Terms of Service ("Terms") constitute a legally binding contract between the entity or individual registering an account ("Client", "User", or "You") and <strong>VortexCloud</strong> ("Company", "we", "us", or "our").
                        </p>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            By creating an account on the VortexCloud platform, checking the registration agreement, or utilizing any provisioned cloud server infrastructure, you certify that you are at least 18 years of age (or the legal age of majority in your jurisdiction) and possess full legal authority to enter into this agreement.
                        </p>
                    </section>

                    <!-- Card 2: Acceptable Use Policy (CRITICAL CLAUSE) -->
                    <section id="section-2" class="bg-white rounded-3xl p-6 sm:p-10 border-2 border-red-200 shadow-sm space-y-5">
                        <div class="flex items-center gap-2 text-red-600 font-bold text-xs uppercase tracking-wider">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <span>Section 2 &bull; Critical Infrastructure Enforcement</span>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Acceptable Use Policy (AUP) — Strictly Prohibited Activities</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            VortexCloud enforces a <strong>Zero-Tolerance Policy</strong> against malicious network activities, illegal operations, and infrastructure abuse. Our automated network monitors constantly inspect connection anomalies. The following activities are <strong>strictly forbidden</strong> across all virtual private servers, IP allocations, and network boundaries:
                        </p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                            <div class="p-4 rounded-xl bg-red-50/60 border border-red-200 space-y-1.5">
                                <h3 class="font-bold text-red-900 text-sm flex items-center gap-2">
                                    <span>🚫</span> Cryptocurrency Mining
                                </h3>
                                <p class="text-xs text-red-800 leading-relaxed">
                                    Running CPU or GPU crypto-mining software (Monero, Bitcoin, Chia, etc.) or blockchain validation algorithms that cause sustained, unfair compute depletion.
                                </p>
                            </div>

                            <div class="p-4 rounded-xl bg-red-50/60 border border-red-200 space-y-1.5">
                                <h3 class="font-bold text-red-900 text-sm flex items-center gap-2">
                                    <span>🚫</span> DDoS Attacks & Network Stressing
                                </h3>
                                <p class="text-xs text-red-800 leading-relaxed">
                                    Originating, relaying, facilitating, or participating in Distributed Denial of Service (DDoS), SYN floods, UDP amplification, booter/stresser scripts, or packet storms.
                                </p>
                            </div>

                            <div class="p-4 rounded-xl bg-red-50/60 border border-red-200 space-y-1.5">
                                <h3 class="font-bold text-red-900 text-sm flex items-center gap-2">
                                    <span>🚫</span> Port Scanning & Reconnaissance
                                </h3>
                                <p class="text-xs text-red-800 leading-relaxed">
                                    Executing unauthorized IP space port sweeps, Masscan, Nmap sweeping, banner grabbing, vulnerability probing, or uninvited security testing against external networks.
                                </p>
                            </div>

                            <div class="p-4 rounded-xl bg-red-50/60 border border-red-200 space-y-1.5">
                                <h3 class="font-bold text-red-900 text-sm flex items-center gap-2">
                                    <span>🚫</span> Phishing & Deceptive Operations
                                </h3>
                                <p class="text-xs text-red-800 leading-relaxed">
                                    Hosting fraudulent banking templates, cloned credential-harvesting landing pages, social engineering campaigns, or malicious redirect chains.
                                </p>
                            </div>

                            <div class="p-4 rounded-xl bg-red-50/60 border border-red-200 space-y-1.5">
                                <h3 class="font-bold text-red-900 text-sm flex items-center gap-2">
                                    <span>🚫</span> Spam, Bulk Email & Open Relays
                                </h3>
                                <p class="text-xs text-red-800 leading-relaxed">
                                    Transmitting unsolicited commercial bulk email (UCE/SPAM), maintaining open mail relays, operating mailbombing utilities, or causing IP addresses to be listed on Spamhaus or RBLs.
                                </p>
                            </div>

                            <div class="p-4 rounded-xl bg-red-50/60 border border-red-200 space-y-1.5">
                                <h3 class="font-bold text-red-900 text-sm flex items-center gap-2">
                                    <span>🚫</span> Malware, Ransomware & Botnets
                                </h3>
                                <p class="text-xs text-red-800 leading-relaxed">
                                    Hosting, distributing, compiling, or controlling trojans, keyloggers, rootkits, command-and-control (C2) servers, botnet handlers, or exploitative binary payloads.
                                </p>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-100 border border-slate-300 text-xs text-slate-700 leading-relaxed">
                            <strong>Enforcement Action:</strong> Any confirmed violation of this Acceptable Use Policy results in <strong>immediate server suspension or termination without prior notice</strong>, zero eligibility for refunds, and permanent forfeiture of all remaining billing cycles. Serious offenses will be reported to appropriate international cybersecurity authorities.
                        </div>
                    </section>

                    <!-- Card 3: Non-Refund & Billing Terms (CRITICAL CLAUSE) -->
                    <section id="section-3" class="bg-white rounded-3xl p-6 sm:p-10 border-2 border-purple-200 shadow-sm space-y-5">
                        <div class="flex items-center gap-2 text-[#673DE6] font-bold text-xs uppercase tracking-wider">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            </svg>
                            <span>Section 3 &bull; Digital Goods Billing Standard</span>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Cancellations, Billing & Non-Refund Policy</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            Because VortexCloud operates high-performance bare-metal hypervisors and instant cloud orchestration systems, services are treated as <strong>custom-allocated digital computing resources</strong>.
                        </p>

                        <div class="p-5 rounded-2xl bg-purple-50/70 border border-purple-200 space-y-3">
                            <h3 class="font-bold text-slate-900 text-base">All Sales are Final Upon Server Provisioning</h3>
                            <p class="text-sm text-slate-700 leading-relaxed">
                                The moment a payment is authorized, our automated provisioning pipeline allocates dedicated vCPU cores, reserves physical DDR5/DDR4 RAM, carves isolated NVMe high-speed storage partitions, and assigns clean public IPv4/IPv6 address blocks from our global datacenter routing tables. 
                            </p>
                            <p class="text-sm text-slate-700 leading-relaxed">
                                <strong>Therefore, all payments for virtual private servers, licenses, and bandwidth add-ons are strictly non-refundable once an instance is provisioned and operational credentials have been delivered.</strong>
                            </p>
                        </div>

                        <ul class="space-y-2.5 text-slate-600 text-sm sm:text-base list-disc list-inside">
                            <li><strong class="text-slate-900">No Partial or Prorated Refunds:</strong> Early cancellation of a monthly, annual (12-month), or biennial (24-month) plan prior to term expiration will not qualify for partial or cash refunds.</li>
                            <li><strong class="text-slate-900">Provisioning Failure Protection:</strong> If an automated system error on our platform prevents a server from being deployed within 24 hours of successful payment confirmation, and our engineering desk cannot resolve the fault, the client is entitled to a full 100% credit applied directly to their internal account balance or original payment method.</li>
                            <li><strong class="text-slate-900">Payment Disputes & Chargebacks:</strong> Unwarranted chargebacks or payment disputes filed with credit card issuers or payment gateways constitute a breach of this agreement and will result in immediate suspension of all associated services and debt referral.</li>
                        </ul>

                        <div class="p-5 rounded-2xl bg-slate-100 border border-slate-200 space-y-2">
                            <h3 class="font-bold text-slate-900 text-sm sm:text-base flex items-center gap-2">
                                <span class="text-[#673DE6]">⬆️</span> Plan Upgrades & Non-Downgrade Architecture Policy
                            </h3>
                            <ul class="space-y-1.5 text-xs sm:text-sm text-slate-700 leading-relaxed list-disc list-inside">
                                <li><strong>VPS Upgrades Allowed Anytime:</strong> Clients may upgrade their server tiers (vCPU, RAM, NVMe storage) at any time. Upgrades are billed on a prorated basis for the remainder of the current billing cycle and take effect immediately without requiring operating system reinstallation.</li>
                                <li><strong>VPS Downgrades Strictly Prohibited:</strong> Downsizing or downgrading an existing VPS instance to a smaller tier is strictly not supported. Under KVM and physical NVMe virtualization architecture, shrinking a virtual disk block partition introduces catastrophic risks of filesystem superblock corruption and irreversible data loss. Clients wishing to utilize a smaller plan must order a new instance and migrate their data independently.</li>
                            </ul>
                        </div>
                    </section>

                    <!-- Card 4: Server Provisioning & Access -->
                    <section id="section-4" class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-4">
                        <span class="text-xs font-bold text-[#673DE6] tracking-wider uppercase">Section 4</span>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Server Provisioning, Root Access & Client Responsibility</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            VortexCloud delivers unmanaged and self-managed virtual private servers with <strong>full root / administrative access</strong>.
                        </p>
                        <ul class="space-y-2 text-slate-600 text-sm sm:text-base list-disc list-inside">
                            <li><strong>Security Maintenance:</strong> You are solely responsible for updating installed operating system packages, patching software vulnerabilities, and configuring firewall rules (UFW, iptables).</li>
                            <li><strong>Data Backups:</strong> Unless you have explicitly subscribed to an automated backup add-on, VortexCloud does not maintain offsite backups of your virtual disks. You are expected to maintain independent off-site data archives.</li>
                            <li><strong>Credential Confidentiality:</strong> You are responsible for preserving the secrecy of your Customer Portal credentials and server SSH root passwords.</li>
                        </ul>
                    </section>

                    <!-- Card 5: SLA & Uptime -->
                    <section id="section-5" class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-4">
                        <span class="text-xs font-bold text-[#673DE6] tracking-wider uppercase">Section 5</span>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Service Level Agreement (SLA) & Network Uptime</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            We guarantee a <strong>99.99% monthly network uptime</strong> for our core routing backbones and power delivery across all Tier-3 and Tier-4 partner datacenters.
                        </p>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            <strong>Exclusions:</strong> Uptime calculations exclude scheduled maintenance announced at least 48 hours in advance, client-induced kernel panics, software configuration errors, upstream transit fiber cuts beyond our network perimeter, or acts of force majeure.
                        </p>
                    </section>

                    <!-- Card 6: Renewal Dunning & Strict Expiration Termination -->
                    <section id="section-6" class="bg-white rounded-3xl p-6 sm:p-10 border-2 border-red-200 shadow-sm space-y-5">
                        <div class="flex items-center gap-2 text-red-600 font-bold text-xs uppercase tracking-wider">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <span>Section 6 &bull; Strict Lifecycle & Termination Rules</span>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Billing Cycles, Renewal Dunning & Zero-Grace-Period Expiration Termination</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            VortexCloud operates as a specialized high-performance cloud infrastructure reseller, reserving dedicated hypervisor compute nodes, physical DDR RAM blocks, and global transit IPv4 routes on non-refundable upstream billing cycles.
                        </p>

                        <div class="space-y-3">
                            <h3 class="font-bold text-slate-900 text-sm sm:text-base">Automated 4-Stage Renewal Notification Schedule</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <div class="p-3.5 rounded-xl bg-purple-50/70 border border-purple-200 space-y-1">
                                    <span class="text-xs font-bold text-[#673DE6] uppercase tracking-wider">14 Days Prior (Day -14)</span>
                                    <p class="text-xs text-slate-700 font-medium">Renewal invoice generated and dispatched to your email and customer portal.</p>
                                </div>
                                <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-200 space-y-1">
                                    <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">7 Days Prior (Day -7)</span>
                                    <p class="text-xs text-slate-700 font-medium">Second automated payment reminder sent if invoice remains unsettled.</p>
                                </div>
                                <div class="p-3.5 rounded-xl bg-amber-50/70 border border-amber-200 space-y-1">
                                    <span class="text-xs font-bold text-amber-700 uppercase tracking-wider">3 Days Prior (Day -3)</span>
                                    <p class="text-xs text-slate-700 font-medium">Urgent 72-hour payment alert sent to prevent service interruption.</p>
                                </div>
                                <div class="p-3.5 rounded-xl bg-red-50/70 border border-red-200 space-y-1">
                                    <span class="text-xs font-bold text-red-700 uppercase tracking-wider">Due Date (Day 0)</span>
                                    <p class="text-xs text-slate-700 font-medium">Final expiration notice warning of immediate cancellation at 23:59 UTC.</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 rounded-2xl bg-red-50/80 border border-red-200 space-y-2">
                            <h3 class="font-bold text-red-900 text-sm sm:text-base flex items-center gap-2">
                                <span>⚠️</span> Strict Non-Holding Policy: Zero Grace Period Upon Expiration
                            </h3>
                            <p class="text-xs sm:text-sm text-red-800 leading-relaxed">
                                <strong>Because upstream datacenter providers bill for recurring compute instances upfront the exact moment a term expires, VortexCloud cannot and does not hold unpaid instances past their due date.</strong>
                            </p>
                            <p class="text-xs sm:text-sm text-red-800 leading-relaxed">
                                If a renewal invoice is not paid and confirmed by <strong>23:59 UTC on the renewal due date</strong>, the virtual private server instance is <strong>immediately cancelled and terminated with zero grace period</strong>. Upon termination:
                            </p>
                            <ul class="text-xs sm:text-sm text-red-800 space-y-1 list-disc list-inside">
                                <li>The virtual machine is immediately destroyed and wiped from datacenter hypervisors.</li>
                                <li>All dedicated IPv4 and IPv6 addresses are released back to global routing tables.</li>
                                <li>All virtual disk volumes and data partitions are permanently deleted with <strong>zero possibility of recovery or restoration</strong>.</li>
                            </ul>
                        </div>
                    </section>

                    <!-- Card 7: Limitation of Liability -->
                    <section id="section-7" class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-4">
                        <span class="text-xs font-bold text-[#673DE6] tracking-wider uppercase">Section 7</span>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Limitation of Liability & Warranty Disclaimer</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            VortexCloud services are provided on an "AS IS" and "AS AVAILABLE" basis. To the maximum extent permitted by applicable law, in no event shall VortexCloud, its directors, employees, or infrastructure suppliers be held liable for any indirect, incidental, punitive, or consequential damages (including loss of profits, business interruption, or data corruption) arising out of the use or inability to use the services.
                        </p>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            The aggregate liability of the Company for all claims relating to the service shall not exceed the total amount paid by the client to VortexCloud during the three (3) months preceding the incident giving rise to liability.
                        </p>
                    </section>

                    <!-- Card 8: Governing Law & Contact -->
                    <section id="section-8" class="bg-gradient-to-tr from-purple-50 via-white to-slate-50 rounded-3xl p-6 sm:p-10 border border-purple-200 shadow-sm space-y-4">
                        <span class="text-xs font-bold text-[#673DE6] tracking-wider uppercase">Section 8</span>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Governing Law & Legal Notices</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            These Terms shall be interpreted, construed, and enforced in accordance with applicable corporate and commercial legal jurisdictions. Formal legal notices or questions regarding these terms may be submitted to our legal counsel:
                        </p>
                        <div class="p-4 bg-white rounded-2xl border border-purple-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div>
                                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Legal Affairs Desk</p>
                                <p class="text-base font-bold text-slate-900 font-mono">
                                    {{ config('services.legal.contact_email', env('LEGAL_CONTACT_EMAIL', 'support@vortexcloud.com')) }}
                                </p>
                            </div>
                            <a href="mailto:{{ config('services.legal.contact_email', env('LEGAL_CONTACT_EMAIL', 'support@vortexcloud.com')) }}" class="btn-shimmer bg-[#673DE6] hover:bg-[#5428D8] text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-md shadow-[#673DE6]/25 transition-all">
                                Contact Legal Desk
                            </a>
                        </div>
                    </section>

                </main>
            </div>
        </div>
    </div>

</x-app-layout>
