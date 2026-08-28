# Resources

> One-line CRUD resource registration with search, filters, sorting, pagination, and auto-generated routes — no controller boilerplate required.

## The Problem

Every entity in a Laravel app needs a controller, routes, search logic, filters, sorting, and field selection. Writing this by hand for every model is repetitive and error-prone.

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        $product = Product::create($validated);

        return ProductResource::make($product);
    }

    // show, update, destroy...
}
```

## The Fuse Way

```php
use Synetro\Fuse\Support\Facades\Fuse;

Fuse::resource(Product::class)
    ->search(['name', 'sku'])
    ->filter(['status', 'category_id'])
    ->sort(['name', 'created_at'])
    ->include(['category', 'brand'])
    ->fields(['id', 'name', 'price', 'status'])
    ->paginate(25)
    ->authorize()
    ->policy(ProductPolicy::class)
    ->register();
```

That's it. Fuse generates the routes, controller, query scoping, and response formatting automatically.

## Installation / Setup

No extra setup needed beyond `php artisan fuse:install`. Resources auto-register routes unless `fuse.routes.auto_register` is disabled in `config/fuse.php`.

## Architecture

The Resource system is composed of several focused classes with clear separation of concerns:

```
Fuse::resource(Product::class)
         │
         ▼
   ResourceBuilder          ← Fluent API. Only builds ResourceDefinition.
         │
         ▼
   ResourceDefinition       ← Immutable value object. Holds all configuration.
         │
         ▼
   ResourceManager          ← Registry. Stores definitions, prevents duplicates,
         │                    owns route registration orchestration.
         ▼
   ResourceRouteRegistrar   ← Registers REST routes with predictable names
         │                    and implicit model binding parameters.
         ▼
   FuseResourceController   ← Internal controller. Handles CRUD without
         │                    developer-created controllers.
         ▼
   ResourceService          ← Business logic. Uses findOrFail semantics.
         │
    ┌────┴────┐
    ▼         ▼
ResourceQuery   ResourcePolicy (Laravel Gate)
    │         │
    └────┬────┘
         ▼
      Eloquent
```

**Key design decisions:**

- `ResourceBuilder` only builds `ResourceDefinition` — it does not register routes.
- `ResourceManager` owns registration and prevents duplicate names/URIs.
- `ResourceRouteRegistrar` handles all route registration, using `{product}`-style parameters for implicit model binding.
- `FuseResourceController` is the default internal controller; developers can override with `->controller()`.
- `ResourcePolicy` delegates to Laravel's `Gate` system, preserving `Gate::before/after`, policy discovery, and `AuthorizationException`.
- `ResourceService` uses `findOrFail()` — missing models throw 404, they don't silently return null.

## Usage

### Register a resource

```php
Fuse::resource(Product::class)
    ->search(['name', 'sku'])
    ->filter(['status'])
    ->sort(['name', 'created_at'])
    ->include(['category', 'brand'])
    ->paginate(25)
    ->authorize()
    ->register();
```

### Generated routes

When registered, the following routes are created automatically:

| Method | URI               | Name           | Action               |
|--------|-------------------|----------------|-----------------------|
| GET    | `/products`       | `products.index` | List/search/filter   |
| POST   | `/products`       | `products.store` | Create new           |
| GET    | `/products/{product}` | `products.show` | Show single         |
| PUT    | `/products/{product}` | `products.update` | Update             |
| PATCH  | `/products/{product}` | `products.update` | Update             |
| DELETE | `/products/{product}` | `products.destroy` | Delete             |

Route parameter uses implicit model binding: `{product}` resolves to a `Product` model instance.

### URI generation

URIs are generated automatically from the model class name:

| Model Class     | Generated URI |
|-----------------|---------------|
| `Product`       | `products`    |
| `ProductImage`  | `product-images` |
| `Category`      | `categories`  |
| `Company`       | `companies`   |

Override with `->uri()`:

```php
Fuse::resource(Product::class)
    ->uri('items')
    ->register();
```

### Custom middleware

Default middleware is `['api']`. Override per resource:

```php
Fuse::resource(Product::class)
    ->middleware(['auth:sanctum', 'throttle:60,1'])
    ->register();
```

### Custom controller

Provide your own controller to take full control:

```php
Fuse::resource(Product::class)
    ->controller(ProductApiController::class)
    ->register();
```

### Build a query manually

```php
$query = Fuse::resource(Product::class)
    ->search(['name', 'sku'])
    ->buildQuery();

$products = $query
    ->search('keyboard')
    ->filter(['status' => 'active'])
    ->sort('-created_at')
    ->include(['category'])
    ->fields(['id', 'name', 'price'])
    ->paginate(25);
```

## Security Considerations

- Use `authorize()` or `policy()` to enforce access control.
- Keep `fields` explicit to prevent over-exposure.
- `search` and `filter` only operate on declared columns — arbitrary columns are silently ignored.
- Route parameter `{resource}` uses implicit model binding for type-safe access.

## Testing

```php
use Synetro\Fuse\Support\Facades\Fuse;
use Synetro\Fuse\Resources\ResourceManager;

class ProductResourceTest extends TestCase
{
    public function test_product_resource_is_registered(): void
    {
        Fuse::resource(Product::class)
            ->search(['name'])
            ->filter(['status'])
            ->register();

        $manager = app(ResourceManager::class);
        $this->assertNotNull($manager->get('Product'));
    }

    public function test_product_index_returns_paginated_data(): void
    {
        $response = $this->get('/products');

        $response->assertOk();
        $response->assertJsonStructure(['data', 'meta']);
    }
}
```

## Extension Points

### Custom ResourceDefinition

Create and register a `ResourceDefinition` directly:

```php
use Synetro\Fuse\Resources\ResourceDefinition;
use Synetro\Fuse\Resources\ResourceManager;

$resource = new ResourceDefinition(
    name: 'Product',
    model: Product::class,
    search: ['name', 'sku'],
    filter: ['status', 'category_id'],
    sort: ['name', 'created_at'],
    include: ['category'],
    fields: ['id', 'name', 'price'],
    paginate: 25,
    authorize: true,
    policy: ProductPolicy::class,
    middleware: ['auth:sanctum'],
);

app(ResourceManager::class)->register($resource);
```

### Custom ResourceRouteRegistrar

Replace the default route registrar:

```php
$this->app->singleton(
    \Synetro\Fuse\Resources\ResourceRouteRegistrar::class,
    function ($app) {
        return new \App\Custom\CustomResourceRouteRegistrar($app['router']);
    }
);
```

## Laravel Equivalent

Wraps manual route registration, controller boilerplate, query-scoped search/filter logic, and response formatting.
