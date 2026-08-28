# Security

> Security headers, diagnostics, and sensitive-data redaction.

## The Problem

Security headers, APP_KEY validation, HTTPS enforcement, and sensitive data redaction are often implemented ad-hoc.

## The Laravel Way

```php
// Middleware
return $next($request)
    ->header('X-Content-Type-Options', 'nosniff')
    ->header('X-Frame-Options', 'DENY');

// Redaction
$redacted = substr($value, 0, 4) . str_repeat('*', strlen($value) - 8) . substr($value, -4);
```

## The Fuse Way

```php
use Synetro\Fuse\Support\Facades\Fuse;

// Security headers
$headers = Fuse::security()->headers();

// Diagnostics
$result = Fuse::security()->check();

// Redact sensitive values
$safe = Fuse::security()->redact($secret);
$token = Fuse::security()->token(32);
```

## Installation / Setup

Run `php artisan fuse:security` to run diagnostics.

```php
'security' => [
    'headers' => [
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'DENY',
        'X-XSS-Protection' => '1; mode=block',
    ],
],
```

## Usage

```php
$headers = Fuse::security()->headers();

$result = Fuse::security()->check();
// SecurityResult with issues array

$safe = Fuse::security()->redact($apiKey);
$token = Fuse::security()->token();
```

## Advanced Usage

```php
// Custom redaction
$safe = Fuse::security()->redact($value, visibleChars: 6);
```

## Security Considerations

- Run `php artisan fuse:security` in CI/CD.
- Ensure `APP_DEBUG` is false in production.

## Testing

```php
$result = Fuse::security()->check();
$this->assertEmpty($result->issues);
```

## Laravel Equivalent

Custom security utility backed by `SecurityResult` and config-driven headers.
