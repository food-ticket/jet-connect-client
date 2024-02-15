<?php

return [
    'api_url' => env('JET_CONNECT_API_URL', 'https://api.flytplatform.com'),
    'api_key' => env('JET_CONNECT_API_KEY'),
    'api_client' => env('JET_CONNECT_API_CLIENT', 'jet-connect-api-client'),
    'webhook_api_key' => env('JET_CONNECT_WEBHOOK_API_KEY'),
    'webhook_secret' => env('JET_CONNECT_WEBHOOK_SECRET'),
];
