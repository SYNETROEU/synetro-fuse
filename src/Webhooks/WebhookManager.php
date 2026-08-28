<?php

declare(strict_types=1);

namespace Synetro\Fuse\Webhooks;

use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Synetro\Fuse\Exceptions\WebhookException;

class WebhookManager
{
    public function __construct(
        protected array $config,
        protected HttpClient $http,
    ) {}

    public function for(string $name): Webhook
    {
        return new Webhook($name, $this);
    }

    public function send(string $url, string $event, mixed $payload, array $options = []): WebhookResponse
    {
        $signature = hash_hmac('sha256', $event . json_encode($payload), $this->config['webhooks']['secret'] ?? config('app.key'));

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Fuse-Event' => $event,
            'X-Fuse-Signature' => $signature,
            'X-Fuse-Timestamp' => now()->timestamp,
        ])->post($url, [
            'event' => $event,
            'payload' => $payload,
            'meta' => [
                'request_id' => request_id(),
                'sent_at' => now()->toIso8601String(),
            ],
        ]);

        return new WebhookResponse($response);
    }

    public function verify(string $payload, string $signature, string $secret): bool
    {
        $expected = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expected, $signature);
    }
}
