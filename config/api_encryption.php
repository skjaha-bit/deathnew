<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Response Encryption
    |--------------------------------------------------------------------------
    |
    | When enabled, user-entered values inside the `data` key of /api/* JSON
    | responses are encrypted with AES-256-GCM. Each encrypted value is sent
    | as "enc:<base64>", so the frontend simply decrypts every string with
    | that prefix and leaves everything else as is.
    |
    | Left plain: ids and foreign keys (`id`, `*_id`, `*_ids`), reference
    | labels next to their foreign key (`bank_name` beside `bank_id`), the
    | keys and routes listed below, and nulls.
    |
    | The key is shared with the frontend, so it is a separate key from
    | APP_KEY - never ship APP_KEY to a browser. Generate one with:
    |
    |     php artisan api-encryption:key
    |
    */

    'enabled' => env('API_ENCRYPTION_ENABLED', true),

    'key' => env('API_ENCRYPTION_KEY'),

    // Routes whose responses are reference/system data, not user input.
    'except_routes' => [
        // Reference (lookup) lists
        'api/v1/banks',
        'api/v1/loan-types',
        'api/v1/account-types',
        'api/v1/bond-types',
        'api/v1/fund-types',
        'api/v1/insurance-companies',
        'api/v1/insurance-types',
        'api/v1/pension-funds',
        'api/v1/trade-unions',
        'api/v1/property-types',
        'api/v1/property-loan-types',
        'api/v1/property-insurance-providers',
        'api/v1/pet-guardians',
        'api/v1/pet-insurance-providers',
        'api/v1/funeral-ceremony-types',
        'api/v1/funeral-arrangement-types',
        'api/v1/funeral-service-types',
        'api/v1/subscription-types',
        'api/v1/subscription-plans',
        'api/v1/countries',
        'api/v1/languages',

        // System content
        'api/v1/translations',
        'api/v1/translations/*',
        'api/v1/banners',
        'api/v1/cms-contents',
        'api/v1/notifications',
        'api/v1/notifications/*',
        'api/v1/overview',
        'api/v1/subscriptions/*',
        'api/v1/pension-fund-inquiries',
    ],

    // Response keys that are never user input.
    'except_keys' => [
        'token',
        'token_type',
        'registration_token',
        'created_at',
        'updated_at',
    ],

    // Keys holding embedded reference objects (e.g. insurance policy types).
    'reference_keys' => [
        'types',
    ],

];
