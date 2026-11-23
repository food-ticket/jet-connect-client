<?php

namespace Foodticket\JetConnect\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Foodticket\JetConnect\JetConnectWebhook;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info(json_encode([(array) $request->query, $request->uri()]));
        Log::info(json_encode($request->array()));

        // Both checks always run: without a configured API key or HMAC secret no webhook can be trusted, so it is
        // rejected instead of let through unchecked.
        if (! $this->hasValidApiKey($request)) {
            report(new \Exception(blank(config('jet-connect.webhook_api_key'))
                ? 'The JET Connect webhook API key is not configured'
                : 'Invalid API key'));

            return response()->json('Invalid API key', 401);
        }

        if (! $this->hasValidSignature($request)) {
            report(new \Exception(blank(config('jet-connect.webhook_hmac_secret'))
                ? 'The JET Connect webhook HMAC secret is not configured'
                : 'Invalid signature'));

            return response()->json('Invalid signature', 401);
        }

        try {
            $webhook = $this->transformNotification($request);

            Event::dispatch($webhook->eventName(), $webhook);

            return response()->noContent(202, ['Content-Type' => 'application/json']);
        } catch (\Throwable $e) {
            report($e);

            return response()->json('Error handling webhook', 500);
        }
    }

    /**
     * @throws Exception
     */
    private function transformNotification(Request $request): JetConnectWebhook
    {
        $notification = $request->all();
        $returnUrl = $request->query('callback');

        return JetConnectWebhook::fromNotification($notification, $returnUrl);
    }

    private function hasValidApiKey(Request $request): bool
    {
        $apiKey = config('jet-connect.webhook_api_key');
        $header = $request->header('Authorization');

        return filled($apiKey) && filled($header) && hash_equals((string) $apiKey, (string) $header);
    }

    private function hasValidSignature(Request $request): bool
    {
        $secret = config('jet-connect.webhook_hmac_secret');
        $hash = $request->header('X-JET-Connect-Hash');

        if (blank($secret) || blank($hash)) {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $request->getContent(), (string) $secret), (string) $hash);
    }
}
