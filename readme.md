# JET Connect API implementation for Laravel

[![GitHub license](https://img.shields.io/github/license/Naereen/StrapDown.js.svg)](https://github.com/Naereen/StrapDown.js/blob/master/LICENSE)

This package allows you to easily make requests to JustEatTakeaway's JET Connect API.

## Requirements

- PHP >= 8.3
- Laravel >= 12.0

## Installation

You can install the package via composer:

```bash
composer require foodticket/jet-connect
```

The package will automatically register itself.

## Configuration
To start using the JET Connect API you will need an API key. Add the API key to your project's .env file:
```php
JET_CONNECT_API_KEY=
```

## Making requests

All endpoints are accessed through `JetConnectApi`:

```php
use Foodticket\JetConnect\Endpoints\JetConnectApi;

$api = new JetConnectApi();
```

---

### setItemAvailability

Mark one or more menu items as available or unavailable for a given restaurant.

```php
use Foodticket\JetConnect\Enums\Availability;

$api->setItemAvailability(
    availability: Availability::UNAVAILABLE,
    itemsIds: ['plu-123', 'plu-456'],
    restaurant: $restaurantId,
    nextAvailableAt: now()->addHours(2), // optional, only used when UNAVAILABLE
);
```

| Parameter | Type | Required | Description |
|---|---|---|---|
| `availability` | `Availability` | Yes | `Availability::AVAILABLE` or `Availability::UNAVAILABLE` |
| `itemsIds` | `array` | Yes | Array of item PLU/reference strings |
| `restaurant` | `string` | Yes | Restaurant identifier |
| `nextAvailableAt` | `Carbon\|null` | No | When the item becomes available again (only applies when marking unavailable) |

---

### ingestMenu

Push a full menu to JET Connect for one or more restaurants.

```php
$api->ingestMenu(
    restaurants: ['restaurant-id-1'],
    menus: [$menuArray],
    callbackUrl: 'https://yourapp.com/jet-connect/menu-callback', // optional
);
```

| Parameter | Type | Required | Description |
|---|---|---|---|
| `restaurants` | `array` | Yes | Array of restaurant identifier strings |
| `menus` | `array` | Yes | Menu payload array |
| `callbackUrl` | `string\|null` | No | URL that JET Connect will call when ingestion completes |

---

### sentToPosSuccess

Confirm that an order was successfully sent to the POS.

```php
$api->sentToPosSuccess(orderId: $orderId);
```

| Parameter | Type | Required | Description |
|---|---|---|---|
| `orderId` | `string` | Yes | The JET Connect order ID |

---

### sentToPosFailed

Report that sending an order to the POS failed.

```php
use Foodticket\JetConnect\Enums\ErrorCode;

$api->sentToPosFailed(
    orderId: $orderId,
    errorCode: ErrorCode::TIMEOUT,
    errorMessage: 'POS did not respond within the timeout window',
);
```

| Parameter | Type | Required | Description |
|---|---|---|---|
| `orderId` | `string` | Yes | The JET Connect order ID |
| `errorCode` | `ErrorCode` | Yes | One of the error codes below |
| `errorMessage` | `string` | Yes | Human-readable description of the failure |

Available `ErrorCode` values:

| Value | Description |
|---|---|
| `AUTH_FAILED` | Order authorization was incorrect |
| `INCORRECT_SETUP` | Configuration details sent with the order are wrong (e.g. store ID) |
| `IN_USE` | POS is currently in use and cannot take requests |
| `INACTIVE` | POS is offline and cannot take orders |
| `MALFORMED_REQUEST` | Order request was malformed (e.g. malformed JSON) |
| `MENU_ERROR` | Order had incorrect items (not in stock, PLU not in POS) |
| `NOT_SUPPORTED` | Integrated ordering is not supported at this restaurant |
| `STORE_CLOSED` | Store is closed and cannot take orders |
| `TENDER_ERROR` | Tender type sent to the POS is wrong |
| `TIMEOUT` | Request to the POS timed out |

---

### orderItemModification

Report that one or more items could not be fulfilled, so JET can update the order accordingly.

```php
$api->orderItemModification(
    orderId: $orderId,
    modifications: [
        [
            'removedItems' => [
                ['plu' => 'plu-123', 'missingQuantity' => 1],
                ['plu' => 'plu-456', 'missingQuantity' => 2],
            ],
        ],
    ],
);
```

| Parameter | Type | Required | Description |
|---|---|---|---|
| `orderId` | `string` | Yes | The JET Connect order ID |
| `modifications` | `array` | Yes | Array of modification objects (see structure below) |

Each modification object:

| Key | Type | Description |
|---|---|---|
| `removedItems` | `array` | Items that could not be fulfilled |

Each `removedItems` entry:

| Key | Type | Description |
|---|---|---|
| `plu` | `string` | PLU code of the item |
| `missingQuantity` | `int` | Number of units that could not be fulfilled |

---

### Custom requests

To call any JET Connect endpoint not covered above:

```php
$api->request()->get('/some-endpoint');
$api->request()->post('/some-endpoint', $payload);
```

## Webhooks
To start receiving webhooks from JET Connect, you need to add the following route the `App\Providers\RouteServiceProvider` file:
```php
$this->routes(function () {
    // ...
    Route::jetConnectWebhooks();
});
```

## Security Vulnerabilities

If you discover a security vulnerability within this project, please report this by email to [developer@foodticket.nl](mailto:developer@foodticket.nl).
