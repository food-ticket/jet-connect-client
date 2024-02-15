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
    | Optionally, if any is configured with JET Connect, set the
    | credentials which the external API will supply when making
    | incoming requests to this application's webhooks.
    |
    */

    'webhook_api_key' => env('JET_CONNECT_WEBHOOK_API_KEY'),

    'webhook_hmac_secret' => env('JET_CONNECT_WEBHOOK_HMAC_SECRET'),
];
