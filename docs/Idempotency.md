# Idempotency

> First-class idempotent execution for payments and distributed systems.

## The Problem

Duplicate webhooks or retried requests can create duplicate orders, charges, or records without protection.

## The Laravel Way

```php
$cacheKey = "idempotent:{$request->header('Idempotency-Key')}";
if (Cache::has($cacheKey)) {
    return Cache::get($cacheKey);
}

$result = DB::transaction(function () use ($data) {
    return Order::create($data);
});

Cache::put($cacheKey, $result, 3600);
return $result;
```

## The Fuse Way

```php
use Synetro\Fuse\Support\Facades\Fuse;

$result = Fuse::idempotent($request->header('Idempotency-Key'))
    ->ttl(3600)
    ->run(fn () => CreateOrder::transaction($data));
```

## Installation / Setup

No setup required. Uses the default cache store.

## Usage

```php
$result = Fuse::idempotent('payment-123')
    ->ttl(3600)
    ->run(function () {
        return Payment::create($data);
    });
```

## Advanced Usage

```php
// Custom TTL per operation
Fuse::idempotent('order-456')->ttl(7200)->run(fn () => processOrder());
```

## Security Considerations

- Use a unique, unpredictable idempotency key from the client.
- TTL should cover the maximum expected retry window.

## Testing

```php
Cache::shouldReceive('get')->andReturn(null);
Cache::shouldReceive('put');
```

## Laravel Equivalent

Wraps `Cache::remember()` with a namespaced key prefix and fluent API.
