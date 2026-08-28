# RateLimit

> Fluent rate limiting using Laravel's RateLimiter underneath.

## The Problem

Throttling login attempts, API calls, or webhook deliveries requires repetitive middleware and cache key management.

## The Laravel Way

```php
$key = "login:{$request->ip()}";
$hits = Cache::add("rate_limit:{$key}", 0, 60);

if (!$hits) {
    $current = Cache::get("rate_limit:{$key}", 0);
    if ($current >= 5) {
        abort(429);
    }
    Cache::increment("rate_limit:{$key}");
}
```

## The Fuse Way

```php
use Synetro\Fuse\Support\Facades\Fuse;

if (!Fuse::limit('login')->perMinute(5)->by($request->ip())->check()) {
    abort(429, 'Too many attempts.');
}
```

## Installation / Setup

No setup required. Uses the default cache store.

## Usage

```php
if (Fuse::limit('api')->perMinute(60)->by($user->id)->check()) {
    return response()->json(['ok' => true]);
}

abort(429);
```

## Advanced Usage

```php
// Per-second limit
if (!Fuse::limit('sensitive')->perSecond(2)->check()) {
    abort(429);
}
```

## Security Considerations

- Identify rate limits by a stable, immutable key (user ID, IP).
- Use different limiters for public and authenticated endpoints.

## Testing

```php
Cache::shouldReceive('add')->andReturn(true);
Cache::shouldReceive('get')->andReturn(0);
```

## Laravel Equivalent

Wraps `Cache::add`, `get`, and `increment` with a fluent API.
