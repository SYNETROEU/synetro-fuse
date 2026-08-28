# Health

> Application health checks for database, cache, queue, and storage.

## The Problem

Monitoring application health requires wiring multiple checks and exposing an endpoint manually.

## The Laravel Way

```php
Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        Cache::get('health-check');
        Queue::connection()->getQueue('');
        Storage::disk('local')->exists('.health');
    } catch (\Throwable $e) {
        return response('unhealthy', 503);
    }

    return response('healthy', 200);
});
```

## The Fuse Way

```php
use Synetro\Fuse\Support\Facades\Fuse;

// Single check
$result = Fuse::health()->check('database');

// All checks
$results = Fuse::health()->all();

// Overall status
$status = Fuse::health()->status(); // 'healthy', 'degraded', or 'failed'
```

## Installation / Setup

Register health checks in `config/fuse.php`:

```php
'health' => [
    'enabled' => true,
    'checks' => [
        \Synetro\Fuse\Health\Checks\DatabaseCheck::class,
        \Synetro\Fuse\Health\Checks\CacheCheck::class,
        \Synetro\Fuse\Health\Checks\QueueCheck::class,
        \Synetro\Fuse\Health\Checks\StorageCheck::class,
    ],
],
```

## Usage

```php
$status = Fuse::health()->status();

$dbResult = Fuse::health()->check('database');

// Register custom check
Fuse::health()->register('redis', new class implements HealthCheckInterface {
    public function check(): HealthResult { /* ... */ }
});
```

## Advanced Usage

```php
// Use the health controller
// GET /fuse/health
```

## Security Considerations

- Do not expose detailed failure reasons on public health endpoints.

## Testing

```php
$this->assertEquals('healthy', Fuse::health()->status());
```

## Laravel Equivalent

Custom health check registry backed by `HealthResult` and `HealthCheckInterface`.
