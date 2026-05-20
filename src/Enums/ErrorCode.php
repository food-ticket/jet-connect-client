<?php

declare(strict_types=1);

namespace Foodticket\JetConnect\Enums;

enum ErrorCode: string
{
    case AUTH_FAILED = 'AUTH_FAILED'; // the order authorization was incorrect.
    case INCORRECT_SETUP = 'INCORRECT_SETUP'; // the configuration details sent with the order are wrong (e.g. store ID)
    case IN_USE = 'IN_USE'; // the POS is currently in use and cannot take requests.
    case INACTIVE = 'INACTIVE'; // the POS is offline and cannot take orders.
    case MALFORMED_REQUEST = 'MALFORMED_REQUEST'; // the order request was malformed (e.g. malformed JSON).
    case MENU_ERROR = 'MENU_ERROR'; // the order had incorrect items (not in stock, PLU not in POS).
    case NOT_SUPPORTED = 'NOT_SUPPORTED'; // integrated ordering is not supported at this restaurant.
    case STORE_CLOSED = 'STORE_CLOSED'; // the store is closed and cannot take orders.
    case TENDER_ERROR = 'TENDER_ERROR'; // the tender_type sent to the POS is wrong.
    case TIMEOUT = 'TIMEOUT'; // the request to the POS timed out.
}
