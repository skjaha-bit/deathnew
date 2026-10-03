<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
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

    'gatewayapi' => [
        'url' => env('GATEWAYAPI_URL', 'https://gatewayapi.com/rest/mtsms'),
        'token' => env('GATEWAYAPI_TOKEN'),
        'sender' => env('GATEWAYAPI_SENDER', 'FYRIRHYGGJA'),
    ],

    'taktikal' => [
        'url' => env('TAKTIKAL_URL', 'https://onboarding.taktikal.is/api'),
        'company_key' => env('TAKTIKAL_COMPANY_KEY'),
        'api_key' => env('TAKTIKAL_API_KEY'),
        'webhook_secret' => env('TAKTIKAL_WEBHOOK_SECRET'),
        'flow_key' => env('TAKTIKAL_FLOW_KEY'),
        'watchlist_deceased_check' => env('TAKTIKAL_WATCHLIST_DECEASED_CHECK', true),
    ],

    'otp' => [
        'length' => env('OTP_LENGTH', 6),
        'expiry_minutes' => env('OTP_EXPIRY_MINUTES', 5),
    ],

];
