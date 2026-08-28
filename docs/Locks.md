# Locks

> Distributed lock wrapper with timeout and automatic release.

## The Problem

Concurrent jobs or requests can race on shared resources (payments, inventory) without atomic locking.

## The Laravel Way

```php
$lock = Cache::lock("payment:{$payment->id}", 60);

if ($lock->get()) {
    try {
        processPayment($payment);
    } finally {
        $lock->release();
    }
} else {
    throw new \RuntimeException("Could not acquire lock");
}
```

## The Fuse Way

```php
use Synetro\Fuse\Support\Facades\Fuse;

Fuse::lock("payment:{$payment->id}")
    ->timeout(60)
    ->run(function ($payment) {
        return processPayment($payment);
    });
```

## Installation / Setup

No setup required. Requires a cache driver that supports atomic locks (Redis, Memcached, database).

## Usage

```php
try {
    Fuse::lock("sync:{$job->id}")
        ->timeout(120)
        ->run(fn () => syncData($job));
} catch (\RuntimeException $e) {
    log_error('Lock acquisition failed', ['job_id' => $job->id]);
}
```

## Advanced Usage

```php
// Longer timeout for slow operations
Fuse::lock("migration:{$id}")->timeout(300)->run(fn () => runMigration());
```

## Security Considerations

- Always set a timeout to prevent deadlocks.
- Release locks in `finally` blocks (handled automatically by Fuse).

## Testing

```php
Cache::shouldReceive('lock')->andReturn(new class {
    public function get() { return true; }
    public function release() {}
});
```

## Laravel Equivalent

Wraps `Cache::lock()` with a fluent interface and automatic release via `finally`.
