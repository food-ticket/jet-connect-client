<?php

declare(strict_types=1);

namespace Foodticket\JetConnect\Endpoints;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

trait OrderItemModification
{
    /**
     * @param  array<int, array{removedItems: array<int, array{plu: string, missingQuantity: int}>}>  $modifications
     *
     * @throws RequestException
     * @throws ConnectionException
     */
    public function orderItemModification(
        string $orderId,
        array $modifications,
    ) {
        $response = $this->request()
            ->post(
                "/orders/$orderId/modification",
                ['modifications' => $modifications]
            );

        if ($response->successful()) {
            return $response->object();
        }

        $response->throw();
    }
}
