# Usage

> Usage tracking, quotas, and entitlements.

## The Problem

Tracking how much of a resource a user or tenant has consumed and enforcing limits requires repetitive cache counters and quota checks.

## The Laravel Way

```php
$key = "usage:{$user->id}:projects";
$used = Cache::get($key, 0);
$limit = 10;

if ($used >= $limit) {
    abort(403, 'Quota exceeded');
}

Cache::put($key, $used + 1, now()->addDay());
```

## The Fuse Way

```php
use Synetro\Fuse\Support\Facades\Fuse;

if (!Fuse::usage($user, 'projects')->limit(10)->consume()) {
    abort(403, 'Quota exceeded');
}

$used = Fuse::usage($user, 'projects')->usage();
```

## Installation / Setup

No setup required. Uses the default cache store.

## Usage

```php
// Consume 1 unit
if (Fuse::usage($user, 'api_calls')->limit(1000)->consume()) {
    // allowed
}

// Consume custom amount
if (Fuse::usage($user, 'storage')->limit(1024)->consume(500)) {
    // allowed
}

// Check current usage
$used = Fuse::usage($user, 'storage')->usage();
```

## Advanced Usage

```php
// Quota by owner object
Fuse::quota('storage')->for($tenant)->limit(10240)->consume(500);

// Reset period
Fuse::usage($user, 'projects')->limit(10)->consume();
```

## Security Considerations

- Keys must be stable and non-guessable.
- Quotas should be enforced server-side, never client-side.

## Testing

```php
Cache::shouldReceive('get')->andReturn(0);
Cache::shouldReceive('put');
```

## Laravel Equivalent

Custom cache-backed counter and quota manager.
