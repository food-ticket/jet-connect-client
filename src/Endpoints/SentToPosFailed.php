<?php

declare(strict_types=1);

namespace Foodticket\JetConnect\Endpoints;

use Foodticket\JetConnect\Enums\ErrorCode;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

trait SentToPosFailed
{
    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function sentToPosFailed(
        string $orderId,
        ErrorCode $errorCode,
        string $errorMessage,
        ?string $apiKey = null,
    ) {
        $data = [
            'happenedAt' => now()->toIso8601String(),
            'errorCode' => $errorCode->value,
            'errorMessage' => $errorMessage,
        ];

        $response = $this->request($apiKey)
            ->post(
                "/order/$orderId/sent-to-pos-failed",
                array_filter($data)
            );

        if ($response->successful()) {
            return $response->object();
        }

        $response->throw();
    }
}
