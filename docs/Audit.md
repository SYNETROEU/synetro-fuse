# Audit

> Automatic model change logging with sensitive field exclusion and event dispatch.

## The Problem

Tracking who changed what and when requires model observers, custom logging, and manual actor attribution.

## The Laravel Way

```php
class ProductObserver
{
    public function updated(Product $product)
    {
        DB::table('audits')->insert([
            'action' => 'updated',
            'model' => Product::class,
            'model_id' => $product->id,
            'old_values' => $product->getOriginal(),
            'new_values' => $product->getChanges(),
            'actor_id' => auth()->id(),
            'created_at' => now(),
        ]);
    }
}
```

## The Fuse Way

```php
use Synetro\Fuse\Traits\Auditable;

class Product extends Model
{
    use Auditable;
}

// Or manually
Fuse::audit()->record(
    action: 'updated',
    model: Product::class,
    modelId: $product->id,
    actorId: auth()->id(),
    oldValues: $original,
    newValues: $changes,
);
```

## Installation / Setup

Run `php artisan fuse:install` to publish the `fuse_audits` migration. Use the `Auditable` trait on models.

```php
'audit' => [
    'enabled' => true,
    'queue' => false,
    'exclude' => ['password', 'api_token', 'remember_token'],
],
```

## Usage

```php
// Via trait
class Product extends Model
{
    use Auditable;
}

// Manual recording
Fuse::audit()->record('created', Product::class, $product->id, auth()->id(), [], $product->toArray());

// Query audits for a model
$audits = Fuse::audit()->forModel(Product::class, $product->id);
```

## Advanced Usage

```php
// With context
Fuse::audit()->record('updated', Product::class, $id, null, [], [], [
    'ip' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);
```

## Security Considerations

- Sensitive fields are excluded via `config('fuse.audit.exclude')`.
- Audit records include IP and request ID for traceability.

## Testing

```php
Audit::where('model', Product::class)->where('model_id', $id)->first();
```

## Laravel Equivalent

Custom model observer backed by an `audits` database table.
