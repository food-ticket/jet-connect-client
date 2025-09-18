# JET Connect API implementation for Laravel

[![GitHub license](https://img.shields.io/github/license/Naereen/StrapDown.js.svg)](https://github.com/Naereen/StrapDown.js/blob/master/LICENSE)

This package allows you to easily make requests to JustEatTakeaway's JET Connect API.

## Requirements

- PHP >= 8.2
- Laravel >= 11.0

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
### itemAvailability
To set an item's availability, you can use the following code:
```php
$jetConnectApi = new JetConnectApi();
$jetConnectApi->setItemAvailability(
    Availability::UNAVAILABLE,
    ['itemReferences'],
    $restaurantId,
    $unavailableTill,
);
```

### Create your own request
If you need to create your own request, you can use the following code:
```php
$jetConnectApi = new JetConnectApi();
$jetConnectApi->request()->get('https://api.flytplatform.com/');
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
