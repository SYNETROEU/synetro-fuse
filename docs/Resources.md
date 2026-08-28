# Resources

> One-line CRUD resource registration with search, filters, sorting, pagination, and auto-generated routes.

## The Problem

Every entity in a Laravel app needs a controller, routes, search logic, filters, sorting, and field selection. Writing this by hand for every model is repetitive.

## The Laravel Way

```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});

// app/Http/Controllers/ProductController.php
class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->orderBy($request->sort ?? 'created_at', $request->dir ?? 'desc')
            ->with(['category']);

        return ProductResource::collection($query->paginate(15));
    }
    // ...
}
```

## The Fuse Way

```php
use Synetro\Fuse\Support\Facades\Fuse;

Fuse::resource(Product::class)
    ->search(['name', 'sku'])
    ->filter(['status', 'category_id'])
    ->sort(['name', 'created_at'])
    ->include(['category'])
    ->fields(['id', 'name', 'price', 'status'])
    ->paginate(25)
    ->policy(ProductPolicy::class)
    ->register();
```

## Installation / Setup

No extra setup needed beyond `php artisan fuse:install`. Resources auto-register routes unless `fuse.routes.auto_register` is disabled in `config/fuse.php`.

## Usage

```php
// Register a resource
Fuse::resource(Product::class)
    ->search(['name', 'sku'])
    ->filter(['status'])
    ->sort(['name', 'created_at'])
    ->include(['category', 'brand'])
    ->paginate(25)
    ->authorize()
    ->register();

// List registered resources
Fuse::resource(Product::class)->manager->all();

// Build a query manually
$query = Fuse::resource(Product::class)->buildQuery();
$products = $query->search('keyboard')->filter(['status' => 'active'])->get();
```

## Advanced Usage

```php
// Custom controller
Fuse::resource(Product::class)
    ->controller(ProductApiController::class)
    ->register();

// Disable route auto-registration
// config/fuse.php
'routes' => [
    'auto_register' => false,
],
```

## Security Considerations

- Use `authorize()` or `policy()` to enforce access control.
- Keep `fields` explicit to prevent over-exposure.
- `search` and `filter` only operate on declared columns.

## Testing

```php
Flow::fake();
Flow::get('/products')->assertOk();
Flow::post('/products', $data)->assertCreated();
```

## Laravel Equivalent

Wraps manual route registration, controller boilerplate, and query-scoped search/filter logic.
