# Tenancy

> Tenant-aware Fuse instances via `Fuse::for($tenant)`.

## The Problem

Multi-tenant apps need scoped configuration, feature flags, and cache keys per tenant without manually prefixing every call.

## The Laravel Way

```php
$tenant = Tenant::find($id);
config(['fuse.billing.currency' => $tenant->currency]);
$key = "tenant:{$tenant->id}:billing.currency";
$currency = Cache::remember($key, 3600, fn () => ...);
```

## The Fuse Way

```php
use Synetro\Fuse\Support\Facades\Fuse;

$tenant = Tenant::find($id);

$currency = Fuse::for($tenant)->config('billing.currency');
$enabled = Fuse::for($tenant)->feature('new-checkout')->enabled();
```

## Installation / Setup

Ensure tenancy middleware sets the current tenant. `FuseTenant` wraps all Fuse calls with the tenant context.

## Usage

```php
// In a tenant-aware controller
public function index(Request $request)
{
    $tenant = $request->tenant();

    return Fuse::for($tenant)
        ->config('app.name');
}
```

## Advanced Usage

```php
// FuseTenant overrides config() to tenant scope
class FuseTenant extends Fuse
{
    public function config(string $key): mixed
    {
        return app(ConfigManager::class)->forTenant($this->tenant)->get($key);
    }
}
```

## Security Considerations

- Ensure tenant isolation is enforced at the middleware or model level.
- Tenant-scoped config must not expose other tenants' secrets.

## Testing

```php
$tenant = Tenant::factory()->create();
$this->assertEquals('EUR', Fuse::for($tenant)->config('billing.currency'));
```

## Laravel Equivalent

Custom tenant-aware wrapper around Fuse's `ConfigManager`.
