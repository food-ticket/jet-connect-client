<?php

declare(strict_types=1);

namespace Foodticket\JetConnect\Enums;

enum Availability: string
{
    case AVAILABLE = 'AVAILABLE';
    case UNAVAILABLE = 'UNAVAILABLE';
}
