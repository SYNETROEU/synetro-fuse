# Bulk

> Bulk update, delete, restore, and chunked iteration over Eloquent query builders.

## The Problem

Updating or deleting thousands of records usually requires writing a loop or a raw SQL statement, both of which risk timeouts and memory exhaustion.

## The Laravel Way

```php
Product::where('status', 'draft')->chunk(100, function ($products) {
    foreach ($products as $product) {
        $product->update(['status' => 'archived']);
    }
});

// Or raw SQL
DB::statement('UPDATE products SET status = "archived" WHERE status = "draft"');
```

## The Fuse Way

```php
use Synetro\Fuse\Support\Facades\Fuse;

// Bulk update
$updated = Fuse::bulk(Product::where('status', 'draft'))->update(['status' => 'archived']);

// Bulk delete
$deleted = Fuse::bulk(Product::whereNull('deleted_at'))->delete();

// Chunked iteration
Fuse::bulk(Product::where('status', 'pending'))->chunk(100, function ($product) {
    $product->process();
});
```

## Installation / Setup

No setup required. `Fuse::bulk()` accepts any `Illuminate\Database\Query\Builder`.

## Usage

```php
$query = Product::where('category_id', 5);

$updated = Fuse::bulk($query)->update(['category_id' => 10]);

$deleted = Fuse::bulk($query)->delete();

$restored = Fuse::bulk(Product::onlyTrashed())->restore();

Fuse::bulk($query)->chunk(500, function ($record) {
    $record->recalculate();
});
```

## Advanced Usage

```php
// Combine with transactions for atomic bulk operations
DB::transaction(function () use ($query) {
    Fuse::bulk($query)->update(['status' => 'archived']);
});
```

## Security Considerations

- Always scope the query to the intended records.
- Use `chunk()` with callbacks when touching individual models to trigger observers.

## Testing

```php
$updated = Fuse::bulk(Product::where('status', 'draft'))->update(['status' => 'archived']);
$this->assertEquals(3, $updated);
```

## Laravel Equivalent

Wraps `Eloquent\Builder::update()`, `delete()`, `restore()`, and `chunk()` with a fluent interface.
