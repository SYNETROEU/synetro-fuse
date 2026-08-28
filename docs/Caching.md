# Caching

> Simple cache helpers with TTL, tags, and batch operations.

## The Problem

Remembering computed values, invalidation, and tagged cache groups requires repeated use of Laravel's `Cache` facade with inconsistent TTLs.

## The Laravel Way

```php
$users = Cache::remember('users', 3600, function () {
    return User::all();
});

Cache::forget('users');

$tags = Cache::tags('users');
$tagged = $tags->remember('active', 3600, fn () => User::active()->get());
```

## The Fuse Way

```php
use Synetro\Fuse\Support\Facades\Fuse;

$users = Fuse::cache('users', fn () => User::all());
$users = Fuse::cacheFor('expensive-data', 600, fn () => ...);
Fuse::forget('users');

$cached = Fuse::cache()->tags(['users', 'active'])->remember('active', fn () => ...);
```

## Installation / Setup

No setup required. Uses the default cache store configured in Laravel.

## Usage

```php
$value = Fuse::cache('key', fn () => compute());
$value = Fuse::cacheFor('key', 300, fn () => compute());
Fuse::forget('key');

$manager = Fuse::cache();
$manager->tags(['users'])->remember('active', fn () => User::active()->get());
$manager->forgetMany(['key1', 'key2']);
```

## Advanced Usage

```php
$many = Fuse::cache()->rememberMany(['a', 'b', 'c'], 600, fn ($key) => ...);
```

## Security Considerations

- Do not cache sensitive user data without proper cache driver encryption.
- Tagged caches require a cache driver that supports tags (Redis, Memcached).

## Testing

```php
Cache::shouldReceive('remember')->andReturn('value');
```

## Laravel Equivalent

Thin wrapper around `Illuminate\Cache\Repository`.
