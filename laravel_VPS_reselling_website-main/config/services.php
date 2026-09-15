<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, Stripe, Contabo, and more.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'stripe' => [
        // Crypto is the platform default. Stripe is strictly disabled by default unless PAYMENT_STRIPE_ENABLED=true AND both keys are present.
        'enabled' => filter_var(env('PAYMENT_STRIPE_ENABLED', false), FILTER_VALIDATE_BOOLEAN) && !empty(env('STRIPE_KEY')) && !empty(env('STRIPE_SECRET')),
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'crypto' => [
        'enabled' => (bool) env('PAYMENT_CRYPTO_ENABLED', true),
        'wallets' => [
            'usdt_trc20' => env('CRYPTO_USDT_TRC20_ADDRESS', 'TPFMfZU4cPcfi3ivmUECDj9bYy5aWdZ4EE'),
            'usdc_polygon' => env('CRYPTO_USDC_POLYGON_ADDRESS', '0x73F701571238739aBce996b6D7358599411FE233'),
            'usdt_polygon' => env('CRYPTO_USDT_POLYGON_ADDRESS', '0x73F701571238739aBce996b6D7358599411FE233'),
        ],
    ],

    'provisioning' => [
        'mode' => env('PROVISIONING_MODE', 'mock'), // 'contabo', 'live', or 'mock'
    ],

    'contabo' => [
        'base_url' => env('CONTABO_API_BASE_URL', 'https://api.contabo.com'),
        'auth_url' => env('CONTABO_AUTH_URL', 'https://auth.contabo.com/auth/realms/contabo/protocol/openid-connect/token'),
        'client_id' => env('CONTABO_CLIENT_ID'),
        'client_secret' => env('CONTABO_CLIENT_SECRET'),
        'api_user' => env('CONTABO_API_USER'),
        'api_password' => env('CONTABO_API_PASSWORD'),
        'default_region' => env('CONTABO_DEFAULT_REGION', 'EU'),
        'default_image_id' => env('CONTABO_DEFAULT_IMAGE_ID', 'afecbb85-e2fc-46f0-9684-b46b1faf00bb'), // Ubuntu 22.04 LTS default
    ],

    'google' => [
        'tag_manager_id' => env('GOOGLE_TAG_MANAGER_ID'),
        'analytics_id' => env('GOOGLE_ANALYTICS_ID'),
        'site_verification' => env('GOOGLE_SITE_VERIFICATION'),
    ],

    'bing' => [
        'site_verification' => env('BING_SITE_VERIFICATION'),
    ],

    'meta' => [
        'pixel_id' => env('META_PIXEL_ID'),
    ],

    'legal' => [
        'contact_email' => env('LEGAL_CONTACT_EMAIL', 'support@vortexcloud.com'),
    ],

];
