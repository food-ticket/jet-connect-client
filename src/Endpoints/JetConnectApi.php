<?php

declare(strict_types=1);

namespace Foodticket\JetConnect\Endpoints;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class JetConnectApi
{
    use ItemAvailability;
    use MenuIngest;

    public function request(): PendingRequest
    {
        return Http::baseUrl(config('jet-connect.api_url'))
            ->asJson()
            ->withHeaders([
                'X-Flyt-Api-Key' => config('jet-connect.api_key'),
                'x-jet-application' => config('jet-connect.api_client'),
            ]);
    }
}
