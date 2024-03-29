<?php

namespace Foodticket\JetConnect;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class JetConnectWebhook
{
    public string $platform = 'jet-connect';

    public function __construct(
        public string $eventName,
        public string $restaurantId,
        public ?string $resourceId,
        public array $payload,
    ) {
    }

    public function platform(): string
    {
        return $this->platform;
    }

    public function eventName(): string
    {
        $eventName = Str::of($this->eventName)->lower()->replace('.', '-');

        return 'jet-connect-webhooks.'.$eventName->toString();
    }

    public function restaurantId(): ?string
    {
        return $this->restaurantId;
    }

    public function resourceId(): ?string
    {
        return $this->resourceId;
    }

    public function payload(): array
    {
        return $this->payload;
    }

    /**
     * @throws Exception
     */
    public static function fromNotification(array $notification): self
    {
        $type = Arr::get($notification, 'type');
        $restaurantId = Arr::get($notification, 'posLocationId') ?? Arr::get($notification, 'location.id');
        $resourceId = Arr::get($notification, 'id');

        if (! $type) {
            throw new Exception();
        }

        return new self(
            $type,
            $restaurantId,
            $resourceId,
            $notification
        );
    }
}
