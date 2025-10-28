<?php

declare(strict_types=1);

namespace Foodticket\JetConnect\Endpoints;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;

trait MenuIngest
{
    /**
     * @throws RequestException
     */
    public function ingestMenu(
        array $restaurants,
        array $menus,
        ?string $callbackUrl = null,
    ) {
        $data = [
            'restaurants' => $restaurants,
            'menus' => $menus,
        ];

        if ($callbackUrl) {
            Arr::add($data, 'callback_url', $callbackUrl);
        }

        $response = $this->request()
            ->post(
                "/menus",
                array_filter($data)
            );

        if ($response->successful()) {
            return $response->object();
        }

        $response->throw();
    }
}
