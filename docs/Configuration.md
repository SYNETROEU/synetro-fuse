# Configuration

> Database-backed, cacheable configuration with typed access and environment overrides.

## The Problem

Configuration values that change at runtime (feature toggles, billing settings) cannot live in `config/*.php` files and need a database-backed store with caching.

## The Laravel Way

```php
// config/fuse.php
return [
    'billing' => [
        'currency' => env('BILLING_CURRENCY', 'USD'),
    ],
];

// To change at runtime
DB::table('settings')->updateOrInsert(['key' => 'billing.currency'], ['value' => 'EUR']);
$currency = Cache::remember('billing.currency', 3600, fn () => DB::table('settings')->where('key', 'billing.currency')->value('value'));
```

## The Fuse Way

```php
use Synetro\Fuse\Support\Facades\Fuse;

// Get
$currency = Fuse::config('billing.currency');

// Set
Fuse::config()->set('billing.currency', 'EUR');

// Typed access
$maxAttempts = Fuse::config('billing.max_attempts')->int();
$company = Fuse::config('company')->array();
```

## Installation / Setup

Run `php artisan fuse:install` to publish the `fuse_configs` migration. Enable caching in `config/fuse.php`:

```php
'config' => [
    'enabled' => true,
    'cache' => true,
    'driver' => 'database',
],
```

## Usage

```php
Fuse::config('app.timezone');
Fuse::config()->set('app.timezone', 'Europe/Helsinki');
Fuse::config()->delete('app.timezone');
$all = Fuse::config()->all();
```

## Advanced Usage

```php
// Tenant-scoped config
$currency = Fuse::for($tenant)->config('billing.currency');
```

## Security Considerations

- Values are serialized before storage. Do not store raw secrets here; use `Fuse::secret()` instead.

## Testing

```php
Fuse::config()->set('test.key', 'value');
$this->assertEquals('value', Fuse::config('test.key'));
```

## Laravel Equivalent

Wraps `Config\Repository`, a `fuse_configs` database table, and `Cache\Repository`.
