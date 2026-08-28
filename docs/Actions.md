# Actions

> Simple action abstraction with synchronous, transactional, and queued execution.

## The Problem

Business logic is often scattered across controllers and jobs, making it hard to reuse, test, or authorize.

## The Laravel Way

```php
class CreateOrder
{
    public function handle(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Order::create($data);
        });
    }
}

// Inline in controller
DB::transaction(function () use ($data) {
    $order = Order::create($data);
    $order->items()->createMany($data['items']);
});
```

## The Fuse Way

```php
use Synetro\Fuse\Actions\Action;

class CreateOrder extends Action
{
    public function handle(mixed $payload): mixed
    {
        return Order::create($payload);
    }
}

// Synchronous
$order = CreateOrder::run($data);

// Transactional
$order = CreateOrder::transaction($data);

// Queued
CreateOrder::queue($data);
```

## Installation / Setup

No setup required. Actions implement a single `handle()` method.

## Usage

```php
$result = CreateOrder::run($data);

$result = CreateOrder::transaction($data);

CreateOrder::queue($data);
```

## Advanced Usage

```php
// Authorize before running
class CreateOrder extends Action
{
    public function authorize(mixed $user, ?string $ability = null): bool
    {
        return $user->can('create orders');
    }
}

// Pipeline
Fuse::pipeline([
    ValidateOrder::class,
    CalculatePrice::class,
    CreateOrder::class,
])->run($orderData);
```

## Security Considerations

- Use `authorize()` to enforce policies before execution.
- Use `transaction()` for operations that must be atomic.

## Testing

```php
Flow::fake();
CreateOrder::run($data);
Flow::assertActionRan(CreateOrder::class);
```

## Laravel Equivalent

Equivalent to plain PHP classes used with `DB::transaction()` and `dispatch()`.
