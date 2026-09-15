@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'robots' => 'index, follow',
    'canonical' => null,
    'ogImage' => null,
    'schema' => null,
])

@php
    $siteName = config('app.name', 'VortexCloud');
    $pageTitle = $title ? $title . ' | ' . $siteName : $siteName . ' - Lightning-Fast NVMe VPS Hosting for Developers';
    $metaDescription = $description ?? 'Lightning-Fast NVMe VPS Hosting for Developers. Deploy high-performance virtual private servers in seconds with dedicated resources, root access, and unmetered bandwidth.';
    $metaKeywords = $keywords ?? 'vps hosting, cloud vps, nvme vps, linux vps, windows rdp, kvm hosting, developer cloud, high frequency vps';
    $canonicalUrl = $canonical ?? url()->current();
    $socialImage = $ogImage ?? url('/images/og-cover.png');
@endphp

<!-- Basic Meta Tags -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="{{ $siteName }}">

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="keywords" content="{{ $metaKeywords }}">
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

@php
    $googleSiteVerification = config('services.google.site_verification', env('GOOGLE_SITE_VERIFICATION'));
    $bingSiteVerification = config('services.bing.site_verification', env('BING_SITE_VERIFICATION'));
@endphp
@if(!empty($googleSiteVerification))
<meta name="google-site-verification" content="{{ $googleSiteVerification }}">
@endif
@if(!empty($bingSiteVerification))
<meta name="msvalidate.01" content="{{ $bingSiteVerification }}">
@endif

<!-- Theme Color for Mobile Browsers -->
<meta name="theme-color" content="#120024">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

<!-- Open Graph / Facebook / Discord / WhatsApp -->
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:image" content="{{ $socialImage }}">

<!-- Twitter / X Cards -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $canonicalUrl }}">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $socialImage }}">

<!-- Global JSON-LD Schema: Organization & WebSite -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@graph": [
        {
            "@@type": "Organization",
            "@@id": "{{ url('/') }}/#organization",
            "name": "{{ $siteName }}",
            "url": "{{ url('/') }}",
            "logo": {
                "@@type": "ImageObject",
                "url": "{{ url('/images/logo.png') }}",
                "caption": "{{ $siteName }}"
            },
            "description": "High-performance NVMe cloud VPS infrastructure and dedicated server instances.",
            "contactPoint": {
                "@@type": "ContactPoint",
                "contactType": "customer support",
                "url": "{{ url('/customer/support-tickets') }}",
                "availableLanguage": ["English"]
            }
        },
        {
            "@@type": "WebSite",
            "@@id": "{{ url('/') }}/#website",
            "url": "{{ url('/') }}",
            "name": "{{ $siteName }}",
            "description": "Lightning-Fast NVMe VPS Hosting for Developers",
            "publisher": {
                "@@id": "{{ url('/') }}/#organization"
            }
        }
    ]
}
</script>

<!-- Page-Specific JSON-LD Schemas -->
@stack('schema')
@isset($schema)
    {!! $schema !!}
@endisset
