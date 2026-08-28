# Query

> Explicit, allow-list-driven query builder for secure search, filter, sort, include, and sparse fieldsets.

## The Problem

Hand-rolling search, filter, sort, include, and sparse field logic for every controller leaks query knowledge across the codebase and exposes arbitrary columns to API consumers.

## The Laravel Way

```php
$query = Product::query();

if ($request->filled('search')) {
    $query->where(function ($q) use ($request) {
        $q->where('name', 'like', "%{$request->search}%")
          ->orWhere('sku', 'like', "%{$request->search}%");
    });
}

if ($request->filled('status')) {
    $query->where('status', $request->status);
}

if ($request->filled('sort')) {
    $query->orderBy($request->sort, $request->dir ?? 'asc');
}

if ($request->filled('include')) {
    $query->with($request->include);
}

if ($request->filled('fields')) {
    $query->select($request->fields);
}

return $query->paginate(25);
```

## The Fuse Way

```php
use Synetro\Fuse\Support\Facades\Fuse;

$fuseQuery = Fuse::resource(Product::class)
    ->search(['name', 'sku'])
    ->buildQuery();

$products = $fuseQuery
    ->search('keyboard')
    ->filter(['status' => 'active'])
    ->sort('-created_at')
    ->include(['category'])
    ->fields(['id', 'name', 'price'])
    ->paginate();
```

## Installation / Setup

Register searchable, filterable, sortable, includable, and field allow-lists via `Fuse::resource()` or instantiate `ResourceQuery` directly.

## Usage

```php
use Synetro\Fuse\Resources\ResourceDefinition;
use Synetro\Fuse\Resources\ResourceQuery;

$definition = new ResourceDefinition(
    name: 'Product',
    model: Product::class,
    search: ['name', 'sku'],
    filter: ['status', 'category_id'],
    sort:   ['name', 'created_at'],
    include: ['category'],
    fields: ['id', 'name', 'price', 'status'],
);

$query = new ResourceQuery($definition);

$products = $query
    ->search('keyboard')
    ->filter(['status' => 'active'])
    ->sort('-created_at')
    ->include(['category'])
    ->fields(['id', 'name', 'price'])
    ->paginate(25);
```

## Advanced Usage

```php
// Apply all params from a request at once
$query->apply($request->all());

// Use with resources
$products = Fuse::resource(Product::class)
    ->search(['name', 'sku'])
    ->buildQuery()
    ->apply($request->all())
    ->paginate();
```

## Security Considerations

- Only declared search/filter/sort/include/fields columns are accepted.
- Unknown filter keys are silently ignored.
- Sparse fieldsets prevent accidental over-exposure.

## Testing

```php
$query = Fuse::query(Product::class)->search('foo');
$this->assertCount(0, $query->get());
```

## Laravel Equivalent

Equivalent to manually scoping `Eloquent\Builder` with conditional `where`, `orderBy`, `with`, and `select` calls.
