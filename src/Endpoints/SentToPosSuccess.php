<?php

declare(strict_types=1);

namespace Foodticket\JetConnect\Endpoints;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

trait SentToPosSuccess
{
    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function sentToPosSuccess(
        string $orderId,
        ?string $apiKey = null,
    ) {
        $data = [
            'happenedAt' => now()->toIso8601String(),
        ];

        $response = $this->request($apiKey)
            ->post(
                "/order/$orderId/sent-to-pos-success",
                array_filter($data)
            );

        if ($response->successful()) {
            return $response->object();
        }

        $response->throw();
    }
}
