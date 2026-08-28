# Metrics

> Counter and timer metrics via a simple, cache-backed manager.

## The Problem

Tracking event counts or durations usually requires database tables or external services.

## The Laravel Way

```php
Metric::increment('orders.created');
$value = Cache::get('metric:orders.created', 0);
```

## The Fuse Way

```php
use Synetro\Fuse\Support\Facades\Fuse;

metric('orders.created')->increment();
metric('payment.amount')->observe($amount);
$value = metric('orders.created')->value();
```

## Installation / Setup

No setup required. Metrics are stored in the default cache store.

## Usage

```php
metric('orders.created')->increment();
metric('orders.created')->increment(5);
metric('payment.amount')->observe($amount);
metric('payment.amount')->decrement(1);

$count = metric('orders.created')->value();
```

## Advanced Usage

```php
// Record via manager directly
Fuse::metrics()->record('orders.created', 1);
$total = Fuse::metrics()->get('orders.created');
```

## Security Considerations

- Do not store sensitive metric values in cache without encryption.

## Testing

```php
metric('test.metric')->increment();
$this->assertEquals(1, metric('test.metric')->value());
```

## Laravel Equivalent

Custom cache-backed metric registry.
