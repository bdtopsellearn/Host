<!-- Vanilla CookieConsent v3 (Free, Open-Source, GDPR/CCPA Compliant) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@v3.0.1/dist/cookieconsent.css">

<!-- Clean SaaS Light Theme with High-Contrast Cosmic Violet Accents -->
<style>
    :root {
        --cc-bg: #FFFFFF !important;
        --cc-primary-color: #0F172A !important;
        --cc-secondary-color: #475569 !important;
        --cc-modal-margin: 1.5rem !important;
        
        /* Primary Button (Accept All) */
        --cc-btn-primary-bg: #673DE6 !important;
        --cc-btn-primary-color: #FFFFFF !important;
        --cc-btn-primary-border-color: #673DE6 !important;
        --cc-btn-primary-hover-bg: #5428D8 !important;
        --cc-btn-primary-hover-color: #FFFFFF !important;
        --cc-btn-primary-hover-border-color: #5428D8 !important;
        
        /* Secondary Buttons (Reject & Preferences) */
        --cc-btn-secondary-bg: #F8FAFC !important;
        --cc-btn-secondary-color: #1E293B !important;
        --cc-btn-secondary-border-color: #CBD5E1 !important;
        --cc-btn-secondary-hover-bg: #F1F5F9 !important;
        --cc-btn-secondary-hover-color: #0F172A !important;
        --cc-btn-secondary-hover-border-color: #94A3B8 !important;
        
        /* Modal & UI Tokens */
        --cc-separator-border-color: #E2E8F0 !important;
        --cc-toggle-on-bg: #673DE6 !important;
        --cc-toggle-off-bg: #CBD5E1 !important;
        --cc-cookie-category-block-bg: #F8FAFC !important;
        --cc-cookie-category-block-border: #E2E8F0 !important;
        --cc-cookie-category-block-hover-bg: #F1F5F9 !important;
        --cc-font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
    }

    /* Consent Modal Container - Strictly Pinned to Bottom Left */
    #cc-main .cm {
        border-radius: 1.25rem !important;
        border: 1px solid #E2E8F0 !important;
        box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.18), 0 0 0 1px rgba(226, 232, 240, 0.9) !important;
        background: #FFFFFF !important;
        max-width: 27rem !important;
        padding: 1.5rem !important;
    }

    #cc-main .cm.cm--box {
        left: 1.5rem !important;
        right: auto !important;
        bottom: 1.5rem !important;
        top: auto !important;
        margin: 0 !important;
    }

    /* Header Title */
    #cc-main .cm__title {
        color: #0F172A !important;
        font-weight: 800 !important;
        font-size: 1.1rem !important;
        letter-spacing: -0.025em !important;
        margin-bottom: 0.5rem !important;
    }

    /* Body Text */
    #cc-main .cm__desc {
        color: #475569 !important;
        font-size: 0.85rem !important;
        line-height: 1.6 !important;
    }

    #cc-main .cm__desc a,
    #cc-main .cm__footer a {
        color: #673DE6 !important;
        font-weight: 600 !important;
        text-decoration: underline !important;
        transition: color 0.15s ease !important;
    }

    #cc-main .cm__desc a:hover,
    #cc-main .cm__footer a:hover {
        color: #5428D8 !important;
    }

    /* High-Contrast Button System */
    #cc-main .cm__btn {
        border-radius: 0.75rem !important;
        font-weight: 700 !important;
        font-size: 0.85rem !important;
        padding: 0.65rem 1.15rem !important;
        min-height: 42px !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        cursor: pointer !important;
    }

    /* 1. Primary "Accept All" Button (Electric Royal Purple) */
    #cc-main .cm__btn:first-child,
    #cc-main .cm__btn:not(.cm__btn--secondary) {
        background: #673DE6 !important;
        color: #FFFFFF !important;
        border: 1px solid #673DE6 !important;
        box-shadow: 0 4px 12px rgba(103, 61, 230, 0.25) !important;
    }

    #cc-main .cm__btn:first-child:hover,
    #cc-main .cm__btn:not(.cm__btn--secondary):hover {
        background: #5428D8 !important;
        border-color: #5428D8 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 16px rgba(103, 61, 230, 0.35) !important;
    }

    /* 2. Secondary "Reject Non-Essential" & "Preferences" Buttons (Clean Slate with High Contrast) */
    #cc-main .cm__btn--secondary {
        background: #F8FAFC !important;
        color: #0F172A !important;
        border: 1px solid #CBD5E1 !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
    }

    #cc-main .cm__btn--secondary:hover {
        background: #EEF2F6 !important;
        border-color: #94A3B8 !important;
        color: #0F172A !important;
        transform: translateY(-1px) !important;
    }

    /* Footer Strip */
    #cc-main .cm__footer {
        padding-top: 0.75rem !important;
        margin-top: 0.75rem !important;
        border-top: 1px solid #F1F5F9 !important;
        font-size: 0.75rem !important;
        color: #64748B !important;
    }

    /* Preferences Modal Window */
    #cc-main .pm {
        border-radius: 1.5rem !important;
        background: #FFFFFF !important;
        border: 1px solid #E2E8F0 !important;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25) !important;
        color: #0F172A !important;
    }

    #cc-main .pm__title {
        color: #0F172A !important;
        font-weight: 800 !important;
    }

    #cc-main .pm__section-title {
        color: #0F172A !important;
        font-weight: 700 !important;
    }

    #cc-main .pm__section-desc {
        color: #475569 !important;
        font-size: 0.875rem !important;
    }

    #cc-main .pm__badge {
        background: #F3E8FF !important;
        color: #673DE6 !important;
        border: 1px solid #E9D5FF !important;
        font-weight: 700 !important;
    }

    #cc-main .pm__btn {
        border-radius: 0.75rem !important;
        font-weight: 700 !important;
    }

    @media (max-width: 640px) {
        #cc-main .cm.cm--box {
            left: 0.75rem !important;
            right: 0.75rem !important;
            bottom: 0.75rem !important;
            max-width: none !important;
        }
    }
</style>

<script defer src="https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@v3.0.1/dist/cookieconsent.umd.js"></script>

<script>
    window.addEventListener('load', function () {
        if (typeof CookieConsent === 'undefined') {
            return;
        }

        CookieConsent.run({
            guiOptions: {
                consentModal: {
                    layout: 'box',
                    position: 'bottom left', // Left side placement
                    equalWeightButtons: false,
                    flipButtons: false
                },
                preferencesModal: {
                    layout: 'box',
                    position: 'left',
                    equalWeightButtons: false,
                    flipButtons: false
                }
            },
            categories: {
                necessary: {
                    readOnly: true,
                    enabled: true
                },
                analytics: {
                    autoClear: {
                        cookies: [
                            { name: /^(_ga|_gid)/ }
                        ]
                    }
                },
                marketing: {}
            },
            language: {
                default: 'en',
                translations: {
                    en: {
                        consentModal: {
                            title: 'Privacy & Cookie Consent',
                            description: 'We use essential cookies to maintain secure sessions and process orders. Optional performance cookies help us refine our platform speed. Read our <a href="/cookie-policy">Cookie Policy</a> for full details.',
                            acceptAllBtn: 'Accept All',
                            acceptNecessaryBtn: 'Reject Non-Essential',
                            showPreferencesBtn: 'Preferences',
                            footer: '<a href="/privacy-policy">Privacy Policy</a> &bull; <a href="/terms-of-service">Terms of Service</a>'
                        },
                        preferencesModal: {
                            title: 'Cookie Preference Center',
                            acceptAllBtn: 'Accept All',
                            acceptNecessaryBtn: 'Reject All',
                            savePreferencesBtn: 'Save Preferences',
                            closeIconLabel: 'Close modal',
                            serviceCounterLabel: 'Service|Services',
                            sections: [
                                {
                                    title: 'Cookie Usage',
                                    description: 'Customize which cookies you wish to allow during your browsing session. Strictly necessary cookies cannot be disabled as they are required for security and checkout transactions.'
                                },
                                {
                                    title: 'Strictly Necessary <span class="pm__badge">Always Enabled</span>',
                                    description: 'Essential for CSRF protection, user authentication, customer portal session persistence, and server routing.',
                                    linkedCategory: 'necessary'
                                },
                                {
                                    title: 'Performance & Analytics',
                                    description: 'Collects anonymous telemetry regarding page loading times, traffic volume, and server error diagnostics.',
                                    linkedCategory: 'analytics'
                                },
                                {
                                    title: 'Marketing & Ad Pixels',
                                    description: 'Allows measurement of developer campaigns and conversion metrics across digital advertising platforms.',
                                    linkedCategory: 'marketing'
                                },
                                {
                                    title: 'More Information',
                                    description: 'For inquiries regarding our cookie management and data handling, please read our <a href="/privacy-policy">Privacy Policy</a> or email <a href="mailto:{{ config('services.legal.contact_email', env('LEGAL_CONTACT_EMAIL', 'support@vortexcloud.com')) }}">{{ config('services.legal.contact_email', env('LEGAL_CONTACT_EMAIL', 'support@vortexcloud.com')) }}</a>.'
                                }
                            ]
                        }
                    }
                }
            }
        });
    });
</script>
