<?php

namespace Foodticket\JetConnect;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class JetConnectWebhook
{
    protected string $platform = 'jet-connect';

    public function __construct(
        protected string $eventName,
        protected string $restaurantId,
        protected ?string $resourceId,
        protected array $payload,
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

    public static function fromNotification(array $notification): self
    {
        $restaurantId = Arr::get($notification, 'location.id') ?? Arr::get($notification, 'posLocationId');
        $resourceId = Arr::get($notification, 'id');

        return new self(
            $notification['type'],
            $restaurantId,
            $resourceId,
            $notification
        );
    }
}
