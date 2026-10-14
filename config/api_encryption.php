<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Response Encryption
    |--------------------------------------------------------------------------
    |
    | When enabled, every value inside the `data` key of an /api/* JSON
    | response is encrypted with AES-256-GCM before it is sent. Ids and
    | foreign keys (`id`, `*_id`, `*_ids`) are left as plain values so the
    | frontend can still route, compare and submit them.
    |
    | The key is shared with the frontend, so it is a separate key from
    | APP_KEY - never ship APP_KEY to a browser. Generate one with:
    |
    |     php artisan api-encryption:key
    |
    */

    'enabled' => env('API_ENCRYPTION_ENABLED', true),

    'key' => env('API_ENCRYPTION_KEY'),

    // Extra response keys (besides id / *_id / *_ids) to leave unencrypted.
    'except_keys' => [],

];
