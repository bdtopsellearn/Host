<!-- Top Left Brand Logo (Clean, Minimalist, Links to Home) -->
<div class="vortex-top-nav">
    <a href="/" class="vortex-nav-logo" title="Back to VortexCloud">
        <div class="vortex-logo-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; display: block;">
                <rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect>
                <rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect>
                <line x1="6" y1="6" x2="6.01" y2="6"></line>
                <line x1="6" y1="18" x2="6.01" y2="18"></line>
            </svg>
        </div>
        <span class="vortex-logo-text">VORTEX<span>CLOUD</span></span>
    </a>
</div>

<!-- Ambient Animated Cosmic Violet Glowing Orbs -->
<div class="vortex-auth-blobs">
    <div class="vortex-blob vortex-blob-1"></div>
    <div class="vortex-blob vortex-blob-2"></div>
</div>

<!-- Native Modern Posh Styling & Keyframe Animations -->
<style>
    /* Reset & Page Base */
    body:has(.fi-simple-layout),
    html:has(.fi-simple-layout) {
        background-color: #0c0017 !important;
        min-height: 100vh !important;
        height: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Cosmic Violet Radial Stage (Vertically Centered, No Forced Scrolling) */
    .fi-simple-layout {
        background: radial-gradient(circle at 50% 20%, #220044 0%, #120024 50%, #080014 100%) !important;
        min-height: 100vh !important;
        position: relative !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 24px 16px !important;
        box-sizing: border-box !important;
    }

    /* Subtle Geometric Grid Matrix */
    .fi-simple-layout::before {
        content: '';
        position: fixed;
        inset: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.07) 1px, transparent 1px);
        background-size: 28px 28px;
        pointer-events: none;
        z-index: 1;
    }

    /* Top Left Logo Header */
    .vortex-top-nav {
        position: fixed;
        top: 28px;
        left: 36px;
        z-index: 100;
        pointer-events: auto;
    }

    @media (max-width: 640px) {
        .vortex-top-nav {
            top: 18px;
            left: 20px;
        }
    }

    .vortex-nav-logo {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .vortex-nav-logo:hover {
        opacity: 0.92;
        transform: translateY(-1px);
    }

    .vortex-logo-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #673DE6 0%, #4F22CC 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 14px rgba(103, 61, 230, 0.4);
        flex-shrink: 0;
    }

    .vortex-logo-text {
        font-size: 19px;
        font-weight: 800;
        letter-spacing: 0.06em;
        color: #FFFFFF;
        text-transform: uppercase;
        font-family: inherit;
        line-height: 1;
    }

    .vortex-logo-text span {
        color: #A855F7;
    }

    /* Ambient Floating Blobs */
    .vortex-auth-blobs {
        position: fixed;
        inset: 0;
        overflow: hidden;
        pointer-events: none;
        z-index: 0;
    }

    .vortex-blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(95px);
        opacity: 0.45;
    }

    .vortex-blob-1 {
        top: -120px;
        left: -80px;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(147, 51, 234, 0.38) 0%, transparent 70%);
        animation: vortexBlobFloatA 14s ease-in-out infinite alternate;
    }

    .vortex-blob-2 {
        bottom: -120px;
        right: -80px;
        width: 520px;
        height: 520px;
        background: radial-gradient(circle, rgba(103, 61, 230, 0.32) 0%, transparent 70%);
        animation: vortexBlobFloatB 16s ease-in-out infinite alternate;
    }

    @keyframes vortexBlobFloatA {
        0% { transform: translateY(0px) scale(1); }
        100% { transform: translateY(40px) scale(1.08); }
    }

    @keyframes vortexBlobFloatB {
        0% { transform: translateY(0px) scale(1); }
        100% { transform: translateY(-40px) scale(0.95); }
    }

    /* Card Container & Alignment */
    .fi-simple-main-ctn {
        width: 100% !important;
        display: flex !important;
        justify-content: center !important;
        position: relative !important;
        z-index: 10 !important;
    }

    /* Compact, Posh Minimalist Card (Fits on Viewport Without Scrolling) */
    .fi-simple-main {
        max-width: 440px !important;
        width: 100% !important;
        background: #FFFFFF !important;
        border: 1px solid rgba(226, 232, 240, 0.95) !important;
        border-radius: 20px !important;
        padding: 32px 34px 28px 34px !important;
        box-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(103, 61, 230, 0.12) !important;
        position: relative !important;
        z-index: 10 !important;
        box-sizing: border-box !important;
        margin: 0 !important;
        animation: authCardEntrance 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
    }

    @media (max-width: 520px) {
        .fi-simple-main {
            padding: 24px 20px 22px 20px !important;
            border-radius: 18px !important;
        }
    }

    @keyframes authCardEntrance {
        from {
            opacity: 0;
            transform: translateY(16px) scale(0.98);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Card Header: Pure Minimalist (No logo inside card, starts directly with Heading) */
    .fi-simple-header {
        text-align: center !important;
        margin-bottom: 20px !important;
    }

    .fi-simple-header .fi-logo {
        display: none !important; /* Logo is cleanly in top-left corner of the page */
    }

    .fi-simple-header-heading,
    .fi-simple-header .fi-header-heading,
    .fi-simple-header h1,
    h1.fi-simple-header-heading {
        font-size: 28px !important;
        font-weight: 800 !important;
        color: #0F172A !important;
        letter-spacing: -0.025em !important;
        line-height: 1.2 !important;
        margin: 0 0 6px 0 !important;
    }

    .fi-simple-header-subheading,
    .fi-simple-header .fi-header-subheading,
    .fi-simple-header p,
    p.fi-simple-header-subheading {
        font-size: 13.5px !important;
        color: #64748B !important;
        margin: 0 !important;
    }

    /* Register & Login Action Links */
    .fi-simple-header a,
    .fi-simple-header .fi-link,
    .fi-simple-page a.fi-link {
        color: #673DE6 !important;
        font-weight: 700 !important;
        text-decoration: none !important;
        border-bottom: 1.5px solid rgba(103, 61, 230, 0.3) !important;
        padding-bottom: 1px !important;
        transition: all 0.15s ease !important;
    }

    .fi-simple-header a:hover,
    .fi-simple-header .fi-link:hover,
    .fi-simple-page a.fi-link:hover {
        color: #5428D8 !important;
        border-bottom-color: #5428D8 !important;
    }

    /* Primary CTA Button */
    .fi-simple-main button[type="submit"],
    .fi-simple-main .fi-btn-primary {
        background: #673DE6 !important;
        border: none !important;
        border-radius: 12px !important;
        font-weight: 700 !important;
        font-size: 14.5px !important;
        letter-spacing: 0.01em !important;
        padding: 12px 20px !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 14px rgba(103, 61, 230, 0.3) !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        cursor: pointer !important;
        width: 100% !important;
    }

    .fi-simple-main button[type="submit"]:hover,
    .fi-simple-main .fi-btn-primary:hover {
        background: #5428D8 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 8px 20px rgba(103, 61, 230, 0.42) !important;
    }

    .fi-simple-main button[type="submit"]:active,
    .fi-simple-main .fi-btn-primary:active {
        transform: scale(0.99) !important;
    }

    /* Filament Input Wrappers (Seamless, Single Outer 10px Rounded Border) */
    .fi-simple-main .fi-input-wrp {
        border-radius: 10px !important;
        border: 1.5px solid #E2E8F0 !important;
        background-color: #FFFFFF !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
        outline: none !important;
        overflow: hidden !important;
    }

    .fi-simple-main .fi-input-wrp:focus-within {
        border-color: #673DE6 !important;
        box-shadow: 0 0 0 3px rgba(103, 61, 230, 0.15) !important;
        --tw-ring-color: transparent !important;
    }

    /* Native Input Reset: No inner borders, no double boxes */
    .fi-simple-main .fi-input-wrp input.fi-input,
    .fi-simple-main input[type="email"],
    .fi-simple-main input[type="password"],
    .fi-simple-main input[type="text"] {
        border: none !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        outline: none !important;
        font-size: 14px !important;
        color: #0F172A !important;
        padding-top: 9px !important;
        padding-bottom: 9px !important;
        padding-left: 13px !important;
        padding-right: 13px !important;
    }

    .fi-simple-main input[type="email"]:focus,
    .fi-simple-main input[type="password"]:focus,
    .fi-simple-main input[type="text"]:focus {
        border: none !important;
        box-shadow: none !important;
        outline: none !important;
    }

    /* Form Labels (Targeting both label element and Filament v3 .fi-fo-field-label-content) */
    .fi-simple-main label,
    .fi-simple-main .fi-fo-field-label,
    .fi-simple-main .fi-fo-field-label-content,
    .fi-simple-main .fi-fo-field-label-content > span,
    .fi-simple-main .fi-checkbox-label {
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #334155 !important;
        margin-bottom: 5px !important;
        line-height: 1.4 !important;
    }

    /* Required Asterisk */
    .fi-simple-main .fi-fo-field-label-required-mark {
        color: #DC2626 !important;
        font-weight: 700 !important;
        margin-left: 2px !important;
    }

    /* Password Reveal Eye Button */
    .fi-simple-main .fi-icon-btn,
    .fi-simple-main .fi-input-wrp-action button,
    .fi-simple-main .fi-ac-icon-btn-action {
        color: #64748B !important;
        transition: color 0.15s ease !important;
    }

    .fi-simple-main .fi-icon-btn:hover,
    .fi-simple-main .fi-input-wrp-action button:hover,
    .fi-simple-main .fi-ac-icon-btn-action:hover {
        color: #0F172A !important;
    }

    /* Forgot Password Link */
    .fi-simple-main a[href*="password-reset"],
    .fi-simple-main .fi-fo-field-wrp-label-actions a {
        color: #673DE6 !important;
        font-weight: 600 !important;
        font-size: 12.5px !important;
        text-decoration: none !important;
        transition: color 0.15s ease !important;
    }

    .fi-simple-main a[href*="password-reset"]:hover,
    .fi-simple-main .fi-fo-field-wrp-label-actions a:hover {
        color: #5428D8 !important;
        text-decoration: underline !important;
    }

    /* Checkbox & Remember Me / Terms Label */
    .fi-simple-main input[type="checkbox"] {
        border-radius: 5px !important;
        border: 1.5px solid #CBD5E1 !important;
        width: 16px !important;
        height: 16px !important;
        cursor: pointer !important;
    }

    .fi-simple-main input[type="checkbox"]:checked {
        background-color: #673DE6 !important;
        border-color: #673DE6 !important;
    }

    /* Terms of Service & Privacy Policy Legal Links (Natural color, clean underline & hover) */
    .fi-simple-main label a,
    .vortex-legal-link {
        color: inherit !important;
        font-weight: 600 !important;
        text-decoration: underline !important;
        text-underline-offset: 3px !important;
        text-decoration-thickness: 1.5px !important;
        transition: opacity 0.2s ease, text-decoration-thickness 0.2s ease !important;
        cursor: pointer !important;
    }

    .fi-simple-main label a:hover,
    .vortex-legal-link:hover {
        color: inherit !important;
        opacity: 0.7 !important;
        text-decoration-thickness: 2px !important;
    }

    /* Absolute Dark-Mode Proofing: Enforces Clean SaaS Light Interior Across All Browsers & Extensions */
    html.dark .fi-simple-main,
    .dark .fi-simple-main {
        background: #FFFFFF !important;
        border-color: rgba(226, 232, 240, 0.95) !important;
        color: #0F172A !important;
    }

    html.dark .fi-simple-header-heading,
    .dark .fi-simple-header-heading {
        color: #0F172A !important;
    }

    html.dark .fi-simple-header-subheading,
    .dark .fi-simple-header-subheading {
        color: #64748B !important;
    }

    html.dark .fi-simple-main label,
    .dark .fi-simple-main label,
    html.dark .fi-simple-main .fi-fo-field-label,
    .dark .fi-simple-main .fi-fo-field-label,
    html.dark .fi-simple-main .fi-fo-field-label-content,
    .dark .fi-simple-main .fi-fo-field-label-content {
        color: #334155 !important;
    }

    html.dark .fi-simple-main .fi-input-wrp,
    .dark .fi-simple-main .fi-input-wrp {
        background-color: #FFFFFF !important;
        border-color: #E2E8F0 !important;
    }

    html.dark .fi-simple-main input.fi-input,
    .dark .fi-simple-main input.fi-input,
    html.dark .fi-simple-main input[type="email"],
    .dark .fi-simple-main input[type="email"],
    html.dark .fi-simple-main input[type="password"],
    .dark .fi-simple-main input[type="password"],
    html.dark .fi-simple-main input[type="text"],
    .dark .fi-simple-main input[type="text"] {
        color: #0F172A !important;
        background-color: transparent !important;
    }
</style>

<script>
    // Enforce Clean SaaS Light Interior across all browsers (including Edge & Brave)
    if (document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.remove('dark');
    }
    try {
        localStorage.setItem('theme', 'light');
    } catch (e) {}
</script>
