# Extending Fuse

> Extending Fuse with custom managers, health checks, discovery classes, generator stubs, macros, and event subscribers.

## The Problem

Every Laravel application needs custom behavior that the package cannot anticipate. Fuse should be extensible without forking or modifying package code.

## The Laravel Way

```php
// Service provider bindings
$this->app->singleton(MyManager::class, function ($app) {
    return new MyManager($app);
});

// Custom middleware
class MyMiddleware {
    public function handle($request, Closure $next) { /* ... */ }
}
```

## The Fuse Way

```php
use Synetro\Fuse\Support\Facades\Fuse;

// Register a custom extension
Fuse::extend('reports', function ($app) {
    return new ReportManager($app['db']);
});

// Register a macro on the Fuse API
Fuse::macro('report', fn (string $name) => Fuse::extension('reports')()->generate($name));

// Usage
Fuse::report('sales');
```

## Extension Points

### 1. Custom Extensions

Register custom managers or services through the Fuse extension system:

```php
use Synetro\Fuse\Support\Facades\Fuse;

Fuse::extend('notifications', function ($app) {
    return new CustomNotificationManager($app['config']);
});

// Retrieve and use
$manager = Fuse::extension('notifications')();
$manager->send($user, 'Hello');
```

### 2. Health Checks

Register custom health checks:

```php
use Synetro\Fuse\Support\Facades\Fuse;
use Synetro\Fuse\Health\Checks\HealthCheckInterface;
use Synetro\Fuse\Health\HealthResult;

class RedisCheck implements HealthCheckInterface
{
    public function check(): HealthResult
    {
        try {
            // Check Redis connection
            return HealthResult::pass('redis');
        } catch (\Throwable $e) {
            return HealthResult::fail('redis', $e->getMessage());
        }
    }
}

Fuse::registerHealthCheck('redis', new RedisCheck());

// Now included in health checks
Fuse::health()->check('redis');
```

### 3. Discovery

Register custom discoverable classes:

```php
use Synetro\Fuse\Support\Facades\Fuse;

// Register a custom Action class for discovery
Fuse::registerDiscovery('Actions', 'App\\Custom\\SendWelcomeEmail');

// Run discovery
$actions = Fuse::auto()->discover('Actions', 'App\\Actions');
```

### 4. Generator Stubs

Customize generated code by registering custom stubs:

```php
use Synetro\Fuse\Support\Facades\Fuse;

Fuse::registerGeneratorStub('controller', resource_path('stubs/fuse/controller.stub'));

// Now `php artisan fuse:make Product --controller` uses your stub
```

### 5. Macros

Add fluent methods to the Fuse facade:

```php
use Synetro\Fuse\Support\Facades\Fuse;

Fuse::macro('tenant', function ($tenant) {
    return new \Synetro\Fuse\Support\FuseTenant($this->app, $tenant);
});

// Usage
Fuse::tenant($tenant)->config('branding.logo');
```

### 6. Event Subscribers

Subscribe to Fuse internal events:

```php
use Synetro\Fuse\Support\Facades\Fuse;

Fuse::subscribe('fuse.audit.recorded', function ($audit) {
    \Log::info('Audit recorded', ['id' => $audit->id]);
});

Fuse::subscribe('fuse.action.ran', function ($action, $result) {
    \Log::info("Action {$action} completed");
});
```

### 7. Container Bindings

Replace any Fuse manager with your own implementation:

```php
// In a service provider
$this->app->singleton(\Synetro\Fuse\Metrics\MetricsManager::class, function ($app) {
    return new CustomMetricsManager($app['cache.store']);
});
```

### 8. Custom Artisan Commands

Register additional commands that integrate with Fuse:

```php
// In a service provider
$this->commands([
    \App\Console\Commands\CustomFuseCommand::class,
]);
```

## Advanced Usage

### Replacing Managers

```php
$this->app->singleton(\Synetro\Fuse\Cache\FuseCacheManager::class, function ($app) {
    return new class($app['cache.store']) extends \Synetro\Fuse\Cache\FuseCacheManager {
        public function remember(string $key, ?callable $callback = null, int $seconds = null): mixed
        {
            // Custom caching logic
            return parent::remember($key, $callback, $seconds);
        }
    };
});
```

### Custom Discovery Types

```php
Fuse::registerDiscovery('CustomType', 'App\\Custom\\Type');

// Extend auto-discovery
$results = Fuse::auto()->discover('CustomType', 'App\\Custom\\Type');
```

### Conditional Extensions

```php
if (config('fuse.features.ai_enabled')) {
    Fuse::extend('ai', function ($app) {
        return new AIService($app['config']['ai']);
    });
}
```

## Security Considerations

- Only register extensions from trusted code. Do not dynamically load extensions from user input or external sources.
- Health checks should not expose sensitive information in their results.
- Custom discovery classes should not expose internal application structure in production.
- Generator stubs should be reviewed for security best practices before use.

## Testing

```php
use Synetro\Fuse\Tests\TestCase;
use Synetro\Fuse\Support\Facades\Fuse;

class ExtensionTest extends TestCase
{
    public function test_custom_extension_can_be_registered(): void
    {
        Fuse::extend('test', fn () => 'value');

        $this->assertTrue(Fuse::hasExtension('test'));
    }

    public function test_health_check_can_be_registered(): void
    {
        $check = new class implements \Synetro\Fuse\Health\Checks\HealthCheckInterface {
            public function check(): \Synetro\Fuse\Health\HealthResult
            {
                return \Synetro\Fuse\Health\HealthResult::pass('test');
            }
        };

        Fuse::registerHealthCheck('test', $check);
        $this->assertArrayHasKey('test', Fuse::extensions()->healthChecks());
    }
}
```

## Laravel Equivalent

Standard Laravel service provider bindings, container extensions, and event subscribers. Fuse's extension system provides a unified API on top of Laravel's container and event dispatcher.
