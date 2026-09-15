<x-app-layout 
    title="Privacy Policy" 
    description="Learn how VortexCloud collects, protects, and handles customer data across our high-performance cloud hosting and NVMe VPS platform."
    keywords="privacy policy, data protection, gdpr compliance, vps privacy, cloud data security"
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
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    <span>Enterprise Data Protection</span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white">
                    Privacy Policy
                </h1>
                <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">
                    Our commitment to data sovereignty, GDPR/CCPA compliance, and robust cryptographic privacy for all cloud infrastructure users.
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
                            <a href="#section-1" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">1. Identity & Scope</a>
                            <a href="#section-2" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">2. Information We Collect</a>
                            <a href="#section-3" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">3. How We Process Data</a>
                            <a href="#section-4" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">4. Infrastructure Security</a>
                            <a href="#section-5" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">5. Payment Processing</a>
                            <a href="#section-6" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">6. Data Retention & Deletion</a>
                            <a href="#section-7" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">7. GDPR & CCPA Rights</a>
                            <a href="#section-8" class="block text-slate-600 hover:text-[#673DE6] hover:translate-x-1 transition-all py-1">8. Contact & Legal Inquiries</a>
                        </nav>
                    </div>
                </aside>

                <!-- Document Body -->
                <main class="lg:col-span-9 space-y-8">
                    
                    <!-- Card 1: Identity & Scope -->
                    <section id="section-1" class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-4">
                        <span class="text-xs font-bold text-[#673DE6] tracking-wider uppercase">Section 1</span>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Identity, Scope & Controller Information</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            This Privacy Policy governs the manner in which <strong>VortexCloud</strong> ("we", "us", "our", or the "Company") collects, utilizes, stores, and protects personal information acquired from registered clients, guest visitors, and automated service consumers across the website and cloud provisioning portal.
                        </p>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            By registering an account, ordering high-performance NVMe virtual instances, or browsing our platform, you acknowledge and agree to the data practices outlined herein. For data privacy legislation purposes (including the General Data Protection Regulation / GDPR and California Consumer Privacy Act / CCPA), VortexCloud operates as the primary Data Controller for account authentication records and metadata.
                        </p>
                    </section>

                    <!-- Card 2: Information We Collect -->
                    <section id="section-2" class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-4">
                        <span class="text-xs font-bold text-[#673DE6] tracking-wider uppercase">Section 2</span>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Information We Collect</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            To deliver enterprise-grade virtual private server provisioning and manage client account authentication, we collect information within the following categories:
                        </p>
                        <ul class="space-y-3 text-slate-600 text-sm sm:text-base list-disc list-inside">
                            <li><strong class="text-slate-900">Account Credentials:</strong> Full name, verified email address, telephone contact (optional), and securely hashed passwords (utilizing Bcrypt multi-round hashing).</li>
                            <li><strong class="text-slate-900">Technical Connection Metadata:</strong> IP addresses, browser fingerprint, operating system user agent, referral headers, and HTTP request timestamps captured in server web logs for DDoS mitigation and unauthorized intrusion detection.</li>
                            <li><strong class="text-slate-900">Server Configuration Attributes:</strong> Selected virtual server resource tiers (vCPU, RAM, NVMe capacity), root password preferences (transmitted strictly over TLS 1.3 encrypted conduits for automated initial configuration), custom hostnames, and datacenter region routing choices.</li>
                            <li><strong class="text-slate-900">Financial Transaction Logs:</strong> Invoice identifiers, transaction hashes (TxID) for cryptocurrency payments, payment timestamps, and masked processor reference IDs. We <em>never</em> store raw credit or debit card numbers on our physical infrastructure.</li>
                        </ul>
                    </section>

                    <!-- Card 3: How We Process Data -->
                    <section id="section-3" class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-4">
                        <span class="text-xs font-bold text-[#673DE6] tracking-wider uppercase">Section 3</span>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Lawful Basis & Purpose of Data Processing</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            We process your personal information strictly under legitimate legal frameworks, including:
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                                <h3 class="font-bold text-slate-900 text-sm mb-1">Contractual Necessity</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">Executing VPS deployment commands, routing IPv4/IPv6 blocks, generating billing receipts, and delivering automated lifecycle notices.</p>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                                <h3 class="font-bold text-slate-900 text-sm mb-1">Network Security & Protection</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">Screening network traffic for outbound DDoS attacks, spam flooding, brute-force intrusions, and compromised host environments.</p>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                                <h3 class="font-bold text-slate-900 text-sm mb-1">Statutory Compliance</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">Maintaining auditable financial ledgers and invoicing records as required by global taxation and commercial regulations.</p>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                                <h3 class="font-bold text-slate-900 text-sm mb-1">Explicit Consent</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">When you choose to activate optional tracking or analytics cookies via our interactive consent management system.</p>
                            </div>
                        </div>
                    </section>

                    <!-- Card 4: Infrastructure Security -->
                    <section id="section-4" class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-4">
                        <span class="text-xs font-bold text-[#673DE6] tracking-wider uppercase">Section 4</span>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Datacenter Infrastructure & Data Sovereignty</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            VortexCloud deploys compute workloads across certified <strong>Tier-3 and Tier-4 global datacenter facilities</strong> located in North America, the European Union, the United Kingdom, and the Asia-Pacific region.
                        </p>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            <strong>Zero Data Monetization:</strong> We do not sell, rent, monetize, or trade customer information, usage habits, or virtual machine disk contents to any third-party marketing brokers or advertisement networks. Server guest storage volumes remain private and encrypted at rest on high-frequency NVMe arrays.
                        </p>
                    </section>

                    <!-- Card 5: Payment Processing -->
                    <section id="section-5" class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-4">
                        <span class="text-xs font-bold text-[#673DE6] tracking-wider uppercase">Section 5</span>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Payment Gateways & Financial Security</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            Payment transactions are routed through audited, PCI-DSS Level 1 compliant payment gateways (such as Stripe) and decentralized public blockchain verification networks (USDT TRC20, USDC Polygon). VortexCloud servers never ingest or store unencrypted payment card PANs or CVVs.
                        </p>
                    </section>

                    <!-- Card 6: Data Retention & Deletion -->
                    <section id="section-6" class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-4">
                        <span class="text-xs font-bold text-[#673DE6] tracking-wider uppercase">Section 6</span>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Data Retention & Scheduled Erasure</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            Account profile records are retained for the active lifecycle of your client membership. Upon service cancellation or termination:
                        </p>
                        <ul class="space-y-2 text-slate-600 text-sm sm:text-base list-disc list-inside">
                            <li>Virtual disk storage arrays and private instance images are scrubbed and permanently overwritten within 24 to 72 hours of server termination.</li>
                            <li>Financial invoicing records and transaction logs are maintained in read-only form for standard accounting compliance periods.</li>
                            <li>Access logs and transient server metrics are purged on a rolling 90-day retention schedule.</li>
                        </ul>
                    </section>

                    <!-- Card 7: GDPR & CCPA Rights -->
                    <section id="section-7" class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-4">
                        <span class="text-xs font-bold text-[#673DE6] tracking-wider uppercase">Section 7</span>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Your Rights Under GDPR & CCPA</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            Regardless of your geographical residency, VortexCloud affords you full data sovereignty rights:
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm pt-2">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <strong>Right to Access:</strong> Request a complete export of your stored personal data records.
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <strong>Right to Rectification:</strong> Update inaccurate or outdated profile details directly via your Customer Portal.
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <strong>Right to Erasure ("Be Forgotten"):</strong> Request complete account decommissioning and personal record purging.
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <strong>Right to Restriction:</strong> Limit automated processing of specific operational metadata.
                            </div>
                        </div>
                    </section>

                    <!-- Card 8: Contact & Legal Inquiries -->
                    <section id="section-8" class="bg-gradient-to-tr from-purple-50 via-white to-slate-50 rounded-3xl p-6 sm:p-10 border border-purple-200 shadow-sm space-y-4">
                        <span class="text-xs font-bold text-[#673DE6] tracking-wider uppercase">Section 8</span>
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Data Protection Officer & Legal Inquiries</h2>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            For legal service requests, GDPR Data Protection inquiries, or to exercise your privacy rights, please reach out to our legal department:
                        </p>
                        <div class="p-4 bg-white rounded-2xl border border-purple-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div>
                                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Official Legal Desk</p>
                                <p class="text-base font-bold text-slate-900 font-mono">
                                    {{ config('services.legal.contact_email', env('LEGAL_CONTACT_EMAIL', 'support@vortexcloud.com')) }}
                                </p>
                            </div>
                            <a href="mailto:{{ config('services.legal.contact_email', env('LEGAL_CONTACT_EMAIL', 'support@vortexcloud.com')) }}" class="btn-shimmer bg-[#673DE6] hover:bg-[#5428D8] text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-md shadow-[#673DE6]/25 transition-all">
                                Send Legal Inquiry
                            </a>
                        </div>
                    </section>

                </main>
            </div>
        </div>
    </div>

</x-app-layout>
