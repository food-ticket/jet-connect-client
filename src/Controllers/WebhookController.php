<?php

namespace Foodticket\JetConnect\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Event;
use Foodticket\JetConnect\JetConnectWebhook;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
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

    /**
     * JET signs the raw request body with the shared secret: the base64 of its HMAC-SHA256 is the signature in the
     * X-JET-Connect-Hash header, "HMAC-SHA256 t=1673428038618,signature=gy7evLHPTUadsmEVw7h6HmOHafCLq4gLKlss1VCV8lI=".
     * The timestamp t is not signed, so it is not checked.
     */
    private function hasValidSignature(Request $request): bool
    {
        $secret = config('jet-connect.webhook_hmac_secret');
        $signature = $this->signatureFromHeader((string) $request->header('X-JET-Connect-Hash'));

        if (blank($secret) || $signature === null) {
            return false;
        }

        $expected = base64_encode(hash_hmac('sha256', $request->getContent(), (string) $secret, true));

        return hash_equals($expected, $signature);
    }

    private function signatureFromHeader(string $header): ?string
    {
        $pattern = '/^HMAC-SHA256\s+(?:.*,)?\s*signature=([A-Za-z0-9+\/]+={0,2})\s*(?:,|$)/';

        if (preg_match($pattern, $header, $matches) !== 1) {
            return null;
        }

        return $matches[1];
    }
}
