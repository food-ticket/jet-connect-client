<?php

declare(strict_types=1);

namespace Foodticket\JetConnect\Endpoints;

use Foodticket\JetConnect\Enums\Availability;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Carbon\Carbon;

trait ItemAvailability
{
    /**
     * @throws RequestException
     */
    public function setItemAvailability(
        Availability $availability,
        array $itemsIds,
        string $restaurant,
        ?Carbon $nextAvailableAt = null,
    ) {
        $data = [
            'event' => $availability->value,
            'itemReferences' => $itemsIds,
            'restaurant' => $restaurant,
        ];

        if ($nextAvailableAt !== null && $availability === Availability::UNAVAILABLE) {
            Arr::add($data, 'nextAvailableAt', $nextAvailableAt->toIso8601String());
        }

        $response = $this->request()
            ->post(
                "/item-availability",
                array_filter($data)
            );

        if ($response->successful()) {
            return $response->object();
        }

        $response->throw();
    }
}
