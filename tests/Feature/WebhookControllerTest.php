<?php

namespace Tests\Feature;

use Foodticket\JetConnect\JetConnectServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\TestResponse;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class WebhookControllerTest extends TestCase
{
    private const string API_KEY = 'webhook-api-key';

    private const string SECRET = 'webhook-hmac-secret';

    protected function getPackageProviders($app): array
    {
        return [JetConnectServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('jet-connect.webhook_api_key', self::API_KEY);
        $app['config']->set('jet-connect.webhook_hmac_secret', self::SECRET);
    }

    protected function defineRoutes($router): void
    {
        Route::jetConnectWebhooks();
    }

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    #[Test]
    public function a_webhook_with_the_api_key_and_a_valid_signature_is_handled(): void
    {
        $this->sendWebhook()->assertStatus(202);

        Event::assertDispatched('jet-connect-webhooks.order-created');
    }

    #[Test]
    public function a_webhook_with_another_api_key_is_rejected(): void
    {
        $this->sendWebhook(apiKey: 'another-key')
            ->assertStatus(401)
            ->assertExactJson(['Invalid API key']);

        Event::assertNotDispatched('jet-connect-webhooks.order-created');
    }

    #[Test]
    public function a_webhook_without_an_api_key_is_rejected(): void
    {
        $this->sendWebhook(apiKey: null)->assertStatus(401);
    }

    #[Test]
    public function a_webhook_is_rejected_when_no_api_key_is_configured(): void
    {
        config(['jet-connect.webhook_api_key' => null]);

        // Before, an unconfigured key let every webhook through, also one without an Authorization header.
        $this->sendWebhook(apiKey: null)->assertStatus(401)->assertExactJson(['Invalid API key']);
    }

    #[Test]
    public function a_webhook_with_a_signature_of_another_body_is_rejected(): void
    {
        $signature = hash_hmac('sha256', '{"type":"other"}', self::SECRET);

        $this->sendWebhook(signature: $signature)->assertStatus(401)->assertExactJson(['Invalid signature']);

        Event::assertNotDispatched('jet-connect-webhooks.order-created');
    }

    #[Test]
    public function a_webhook_without_a_signature_is_rejected(): void
    {
        $this->sendWebhook(signature: null)->assertStatus(401)->assertExactJson(['Invalid signature']);
    }

    #[Test]
    public function a_webhook_is_rejected_when_no_hmac_secret_is_configured(): void
    {
        config(['jet-connect.webhook_hmac_secret' => null]);

        $this->sendWebhook()->assertStatus(401)->assertExactJson(['Invalid signature']);
    }

    /**
     * @param  string|null  $signature  '' signs the body with the configured secret, null sends no signature
     */
    private function sendWebhook(?string $apiKey = self::API_KEY, ?string $signature = ''): TestResponse
    {
        $body = json_encode(['type' => 'Order.Created', 'id' => 'order-1', 'posLocationId' => 'location-1']);
        $signature = $signature === '' ? hash_hmac('sha256', $body, self::SECRET) : $signature;

        $headers = array_filter([
            'Authorization' => $apiKey,
            'X-JET-Connect-Hash' => $signature,
            'Content-Type' => 'application/json',
        ], fn (?string $value): bool => $value !== null);

        $server = $this->transformHeadersToServerVars($headers);

        return $this->call('POST', 'jet-connect/webhooks', [], [], [], $server, $body);
    }
}
