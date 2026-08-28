# Extending

> Extending Fuse with custom managers, middleware, console commands, and discovery.

## The Problem

Every project needs custom behavior that the package cannot anticipate.

## The Laravel Way

```php
// Service provider bindings
$this->app->singleton(MyManager::class, function ($app) {
    return new MyManager();
});

// Middleware
class MyMiddleware {
    public function handle($request, Closure $next) { /* ... */ }
}
```

## The Fuse Way

```php
// In a service provider
public function register(): void
{
    $this->app->singleton(MyManager::class, function ($app) {
        return new MyManager($app);
    });
}

// Extend Fuse facade methods
Fuse::extend('custom', function ($app) {
    return new MyManager($app);
});
```

## Installation / Setup

Create a service provider and bind custom managers into the container.

## Usage

```php
// Custom action
class CustomAction extends Action
{
    public function handle(mixed $payload): mixed
    {
        // custom logic
    }
}

// Custom health check
class CustomCheck implements HealthCheckInterface
{
    public function check(): HealthResult
    {
        return HealthResult::pass('custom');
    }
}

Fuse::health()->register('custom', new CustomCheck());
```

## Advanced Usage

```php
// Custom stubs for generators
// Place in stubs/ directory and configure in config/fuse.php
```

## Security Considerations

- Custom managers should validate inputs and authorize actions.
- Never bind unvalidated external code into the container.

## Testing

```php
$this->app->singleton(MyManager::class, fn () => new MyManager());
$this->assertInstanceOf(MyManager::class, app(MyManager::class));
```

## Laravel Equivalent

Standard Laravel service provider bindings and container extensions.
