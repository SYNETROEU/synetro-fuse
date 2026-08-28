# ImportExport

> CSV and JSON import/export with chunking, failed-row reporting, and per-row callbacks.

## The Problem

Importing CSVs or exporting large datasets requires parsing, chunking, error handling, and formatting logic that is tedious to rewrite for every entity.

## The Laravel Way

```php
use League\Csv\Reader;

$csv = Reader::createFromPath($file, 'r');
foreach ($csv->getRecords() as $offset => $record) {
    try {
        Product::create($record);
    } catch (\Throwable $e) {
        $failed[] = ['row' => $offset, 'error' => $e->getMessage()];
    }
}

// Export
$products = Product::all();
$headers = array_keys($products->first()->toArray());
$rows = $products->map(fn ($p) => implode(',', $p->toArray()));
$csv = implode("\n", array_merge([implode(',', $headers)], $rows->toArray()));
```

## The Fuse Way

```php
use Synetro\Fuse\Support\Facades\Fuse;

// Import
$result = Fuse::import(Product::class, $file, function ($product, $index) {
    $product->calculateTax();
});

// Export
$csv = Fuse::export(Product::class, 'csv');
$json = Fuse::export(Product::class, 'json');
```

## Installation / Setup

No extra setup. Ensure the target model is fillable for mass assignment.

## Usage

```php
$result = Fuse::import(Product::class, 'storage/app/products.csv', function ($product, $index) {
    $product->slug = Str::slug($product->name);
});

// $result = ['imported' => 42, 'failed' => [['row' => 7, 'error' => '...']]]

$csv = Fuse::export(Product::class, 'csv');
$json = Fuse::export(Product::class, 'json');
```

## Advanced Usage

```php
// Import from an array directly
$result = Fuse::import(Product::class, [
    ['name' => 'Product A', 'price' => 100],
    ['name' => 'Product B', 'price' => 200],
]);
```

## Security Considerations

- Validate imported data before relying on it.
- Never import into models without controlled fillable attributes.

## Testing

```php
$result = Fuse::import(Product::class, $csvFile);
$this->assertGreaterThan(0, $result['imported']);
```

## Laravel Equivalent

Combines `Storage` file parsing, `Model::create()`, and manual CSV string building into a single call.
