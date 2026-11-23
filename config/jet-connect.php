<?php

return [
    /*
    |------------------------------------------------------------
    | JET Connect API configuration
    |------------------------------------------------------------
    |
    | Set the endpoints root URL, API key and (optionally) the
    | client identifier to use when making requests to the JET
    | Connect API.
    |
    */

    'api_url' => env('JET_CONNECT_API_URL', 'https://api.flytplatform.com'),

    'api_key' => env('JET_CONNECT_API_KEY'),

    'api_client' => env('JET_CONNECT_API_CLIENT', 'jet-connect-client'),

    /*
    |------------------------------------------------------------
    | Application webhook configuration
    |------------------------------------------------------------
    |
    | The credentials JET Connect supplies when making incoming requests to
    | this application's webhooks. Both are required: a webhook is rejected
    | when either is not configured.
    |
    */

    'webhook_api_key' => env('JET_CONNECT_WEBHOOK_API_KEY'),

    'webhook_hmac_secret' => env('JET_CONNECT_WEBHOOK_HMAC_SECRET'),
];
