<?php

namespace Foodticket\JetConnect;

use Illuminate\Foundation\Application;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use Foodticket\JetConnect\Controllers\WebhookController;

class JetConnectServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishConfig();
        $this->registerMacros();
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        if ($this->app instanceof Application && $this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/jet-connect.php' => config_path('jet-connect.php'),
            ], 'uber-eats-config');
        }
    }

    protected function publishConfig(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/jet-connect.php' => config_path('jet-connect.php'),
            ], 'uber-eats');
        }

        $this->mergeConfigFrom(__DIR__ . '/../config/jet-connect.php', 'jet-connect');
    }

    protected function registerMacros(): void
    {
        Route::macro('jetConnectWebhooks', function (string $uri = 'jet-connect/webhooks') {
            return $this->post($uri, [WebhookController::class, 'handle'])->name('jet-connect.webhooks');
        });
    }
}
