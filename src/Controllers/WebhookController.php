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
        if ($this->shouldValidateApiKey() && ! $this->hasValidApiKey($request)) {
            return response()->json('Invalid API key', 401);
        }

        if ($this->shouldValidateSignature() && ! $this->hasValidSignature($request)) {
            return response()->json('Invalid signature', 401);
        }

        try {
            $webhook = $this->transformNotification($request);

            Log::info('Webhook received: ' . json_encode([$webhook, $request]));

            Event::dispatch($webhook->eventName(), $webhook);

            return response()->noContent(200, ['Content-Type' => 'application/json']);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return response()->json('Error handling webhook', 500);
        }
    }

    /**
     * @throws Exception
     */
    private function transformNotification(Request $request): JetConnectWebhook
    {
        $notification = $request->all();

        return JetConnectWebhook::fromNotification($notification);
    }

    private function shouldValidateApiKey(): bool
    {
        return ! empty(config('jet-connect.webhook_api_key'));
    }

    private function hasValidApiKey(Request $request): bool
    {
        return $request->header('Authorization') === config('jet-connect.webhook_api_key');
    }

    private function shouldValidateSignature(): bool
    {
        return ! empty(config('jet-connect.webhook_hmac_secret'));
    }

    private function hasValidSignature(Request $request): bool
    {
        $signature = hash_hmac('sha256', $request->getContent(), config('jet-connect.webhook_hmac_secret'));

        return hash_equals($request->header('X-JET-Connect-Hash'), $signature);
    }
}
