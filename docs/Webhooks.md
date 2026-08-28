# Webhooks

> Outgoing webhooks with HMAC signatures, timestamps, and queued delivery.

## The Problem

Sending webhooks requires signing payloads, adding timestamps, and handling retries manually.

## The Laravel Way

```php
$payload = [
    'event' => 'invoice.created',
    'payload' => $invoice->toArray(),
];

$signature = hash_hmac('sha256', json_encode($payload), config('app.key'));

Http::withHeaders([
    'X-Fuse-Signature' => $signature,
    'X-Fuse-Timestamp' => now()->timestamp,
])->post($url, $payload);
```

## The Fuse Way

```php
use Synetro\Fuse\Support\Facades\Fuse;
use Synetro\Fuse\Webhooks\Webhook;

Webhook::send($url, 'invoice.created', $invoice)
    ->signed()
    ->queued()
    ->retry(5)
    ->timeout(10)
    ->send();
```

## Installation / Setup

Configure webhook secrets in `config/fuse.php`:

```php
'webhooks' => [
    'enabled' => true,
    'secret' => env('FUSE_WEBHOOK_SECRET'),
    'signature_header' => 'X-Fuse-Signature',
],
```

## Usage

```php
$response = Webhook::send($url, 'user.created', $user)
    ->signed()
    ->queued()
    ->retry(3)
    ->timeout(10)
    ->send();
```

## Advanced Usage

```php
// Verify incoming webhooks
$valid = Fuse::webhook('incoming')->verify($payload, $signature, $secret);
```

## Security Considerations

- Always verify webhook signatures server-side.
- Validate the `X-Fuse-Timestamp` to prevent replay attacks.

## Testing

```php
Http::fake([
    '*' => Http::response('ok', 200),
]);
```

## Laravel Equivalent

Wraps `Illuminate\Http\Client\Http` with HMAC signing and metadata headers.
