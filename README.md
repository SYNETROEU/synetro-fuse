<!-- Fuse README.md - GitHub Ready -->

<div align="center">

# Fuse

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php)
![Laravel](https://img.shields.io/badge/Laravel-11%2B-FF2D20?style=flat-square&logo=laravel)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)
![Status](https://img.shields.io/badge/Status-Active-success?style=flat-square)

**Powerful Laravel applications. Without the boilerplate.**

[Installation](#installation) &nbsp;•&nbsp; [Quick Start](#quick-start) &nbsp;•&nbsp; [Features](#features) &nbsp;•&nbsp; [Documentation](#documentation) &nbsp;•&nbsp; [Testing](#testing) &nbsp;•&nbsp; [Contributing](#contributing)

</div>

---

## What is Fuse?

Fuse is a developer productivity layer for Laravel. Its purpose is extremely simple:

> **Eliminate repetitive Laravel glue code while keeping Laravel itself intact.**

Instead of manually wiring controllers, form requests, resources, policies, filters, routes, and tests for every entity, Fuse lets you write:

```php
Fuse::resource(Product::class);
```

And still drop down into normal Laravel whenever you need to:

```php
Product::query()
DB::transaction(...)
Route::get(...)
Cache::remember(...)
Mail::to(...)
```

---

## Installation

```bash
composer require synetro/fuse
```

### Requirements

- PHP 8.2 or higher
- Laravel 11.x / 12.x / 13.x
- OpenSSL extension (for secrets encryption)

### Setup

```bash
php artisan fuse:install
```

This will:
- Publish configuration to `config/fuse.php`
- Publish database migrations
- Register optional middleware
- Verify requirements

### Verify Installation

```bash
php artisan fuse:doctor
```

---

## Quick Start

```php
use Synetro\Fuse\Support\Facades\Fuse;

// Register a resource with full CRUD, search, filters, and pagination
Fuse::resource(Product::class)
    ->search(['name', 'sku'])
    ->filter(['status', 'category_id'])
    ->sort(['name', 'created_at'])
    ->include(['category'])
    ->fields(['id', 'name', 'price'])
    ->paginate(25)
    ->authorize();

// Configuration (DB-backed, cached)
Fuse::config('billing.currency');
Fuse::config()->set('billing.currency', 'EUR');
Fuse::config('billing.max_attempts')->int();
Fuse::config('company')->array();

// Feature flags
Fuse::feature('new-dashboard')->enabled();
Fuse::feature('new-checkout')->rollout(25)->enabled();

// Secrets (encrypted at rest, redacted from logs)
Fuse::secret('stripe.secret')->get();
Fuse::secret('stripe.secret')->set($secret);

// Cache helpers
Fuse::cache('users', fn () => User::all());
Fuse::cacheFor('expensive-data', 600, fn () => ...);
Fuse::forget('users');

// Health checks
Fuse::health()->status();
Fuse::health()->check('database');

// Webhooks (outgoing)
Webhook::send($url, 'invoice.created', $invoice)
    ->signed()
    ->queued()
    ->retry(5)
    ->timeout(10);

// Actions
class CreateOrder extends Action
{
    public function handle(array $data)
    {
        return Order::create($data);
    }
}

CreateOrder::run($data);
CreateOrder::transaction($data);
CreateOrder::queue($data);

// Pipelines
Fuse::pipeline([
    ValidateOrder::class,
    CalculatePrice::class,
    ChargeCustomer::class,
    CreateOrder::class,
])->run($order);

// API responses
return api()->created($user);
return api()->updated($user);
return api()->deleted();
return api()->error('USER_NOT_FOUND', 'User does not exist.');

// Logging
Fuse::log()
    ->event('invoice.created')
    ->user($user)
    ->context($invoice)
    ->write();

log_info('Order created', ['id' => $order->id]);
log_warning('Low stock', ['sku' => $product->sku]);
log_error('Payment failed', ['order_id' => $order->id]);

// Metrics
metric('orders.created')->increment();
metric('payment.amount')->observe($amount);

// Background tasks
background(fn () => expensiveOperation());
later(300, fn () => sendReminder());

// Notifications
notify($user, 'Welcome!');
notify($user)
    ->email()
    ->database()
    ->broadcast();

// File attachments
$user->attachFile('avatar', $uploadedFile);
$user->file('avatar')->url();
$user->file('avatar')->temporaryUrl(now()->addHours(2));

// Security
Fuse::security()->headers();
Fuse::security()->check();
Fuse::security()->redact($secret);

// Query system
Product::fuse()
    ->search('keyboard')
    ->filter(['status' => 'active'])
    ->sort('-created_at')
    ->include(['category'])
    ->fields(['id', 'name', 'price'])
    ->paginate();

// Reusable filters
Fuse::filter('active', fn ($query) => $query->where('status', 'active'));
Product::fuse()->filter('active')->get();
```

---

## Features

| Feature | Description |
|---------|-------------|
| **Resources / CRUD** | One-line resource registration with search, filters, sorting, pagination, and authorization |
| **Query System** | Secure, explicit query building with allow-lists for search, filter, sort, include, and fields |
| **Actions** | Simple action abstraction with transactions, queues, and events |
| **API Responses** | Consistent JSON envelope, pagination metadata, and machine-readable errors |
| **DB Configuration** | Database-backed configuration with caching, typed access, and environment overrides |
| **Secrets** | Encrypted secrets management with log redaction and rotation support |
| **Feature Flags** | Global, user-targeted, and percentage rollout feature flags |
| **Caching** | Simple cache helpers with automatic invalidation |
| **Webhooks** | Outgoing webhooks with HMAC signatures, retries, and queued delivery |
| **Audit Logging** | Automatic model auditing with sensitive field exclusion |
| **Health Checks** | Database, cache, queue, and storage health monitoring |
| **Security** | Security headers, suspicious login detection, and sensitive-data redaction |
| **Metrics** | Counter and timer metrics |
| **Files** | File attachment helpers built on Laravel Filesystem |
| **Notifications** | Simple notification routing |
| **Logging** | Structured logging with automatic request context |
| **Pipelines** | Synchronous and queued workflow orchestration |
| **Testing** | Flow testing helpers, fake managers, and assertions |
| **Generators** | Code generators for models, controllers, actions, tests, and full CRUD |
| **OpenAPI** | Automatic OpenAPI documentation generation |
| **Artisan Commands** | `install`, `doctor`, `optimize`, `make`, `openapi`, `inspect`, and more |

---

## Documentation

### Core Concepts

- [Resources & CRUD](docs/Resources.md)
- [Query System](docs/Query.md)
- [Actions & Pipelines](docs/Actions.md)
- [API Responses](docs/Api.md)
- [Configuration](docs/Configuration.md)
- [Secrets](docs/Secrets.md)
- [Feature Flags](docs/Features.md)
- [Webhooks](docs/Webhooks.md)
- [Audit Logging](docs/Audit.md)
- [Health Checks](docs/Health.md)
- [Testing](docs/Testing.md)
- [Security](docs/Security.md)
- [Generators](docs/Generators.md)
- [Extending Fuse](docs/Extending.md)

### Artisan Commands

```bash
php artisan fuse:install          # Install the package
php artisan fuse:doctor           # Run diagnostics
php artisan fuse:optimize         # Optimize caches
php artisan fuse:make Product     # Generate a component
php artisan fuse:make Product --full  # Generate full CRUD
php artisan fuse:about            # Application overview
php artisan fuse:routes           # List Fuse routes
php artisan fuse:models           # List models
php artisan fuse:health           # Health check
php artisan fuse:openapi          # Generate OpenAPI spec
php artisan fuse:docs             # Generate documentation
php artisan fuse:inspect User     # Inspect a model
```

---

## Configuration

Publish and edit `config/fuse.php`:

```php
return [
    'cache' => [
        'enabled' => true,
        'ttl' => 3600,
    ],

    'config' => [
        'cache' => true,
        'driver' => 'database',
    ],

    'features' => [
        'enabled' => true,
        'cache' => true,
    ],

    'secrets' => [
        'encryption' => true,
        'redact_from_logs' => true,
    ],

    'audit' => [
        'enabled' => true,
        'queue' => false,
    ],

    'webhooks' => [
        'enabled' => true,
        'signature_header' => 'X-Fuse-Signature',
    ],

    'api' => [
        'envelope' => true,
    ],

    'health' => [
        'enabled' => true,
        'checks' => [
            \Synetro\Fuse\Health\Checks\DatabaseCheck::class,
            \Synetro\Fuse\Health\Checks\CacheCheck::class,
            \Synetro\Fuse\Health\Checks\QueueCheck::class,
            \Synetro\Fuse\Health\Checks\StorageCheck::class,
        ],
    ],
];
```

---

## Testing

```php
use Synetro\Fuse\Testing\Flow;

Flow::fake();

Flow::post('/users', $data)->assertCreated();
Flow::assertActionRan(CreateUser::class);
Flow::assertWebhookSent('user.created');
```

### Running Tests

```bash
vendor/bin/phpunit
```

---

## Security

Fuse takes security seriously. If you discover a security vulnerability, please report it responsibly:

1. **Do not** open a public GitHub issue
2. Email security@synetro.dev with details
3. Allow time for the issue to be patched before public disclosure

### Security Features

- Secrets are encrypted at rest using Laravel's encrypter
- Secrets are automatically redacted from logs
- Webhook signatures are verified using HMAC-SHA256
- Webhook replay attacks are prevented via timestamp validation
- Query filters use explicit allow-lists — no arbitrary SQL exposure
- All model fields exposed via API must be explicitly configured
- Audit logs exclude sensitive fields by default (`password`, `api_token`, etc.)
- Security headers are configurable and enabled by default

---

## Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

### Development Setup

```bash
git clone https://github.com/synetro/fuse.git
cd fuse
composer install
vendor/bin/phpunit
```

### Code Style

```bash
vendor/bin/pint
```

---

## Roadmap

- [ ] Full CRUD generator with tests, factories, and seeders
- [ ] Multi-tenancy integration
- [ ] Admin UI metadata for settings
- [ ] Image processing helpers (when intervention/image is installed)
- [ ] Advanced query filters (whereBetween, whereDate, whereNull, aggregates)
- [ ] Scheduled activation for feature flags
- [ ] Webhook replay support
- [ ] Deployment checks (`fuse:deploy-check`)
- [ ] Module system (`fuse:make-module`)

See [ROADMAP.md](ROADMAP.md) for details.

---

## License

Fuse is open-source software licensed under the [MIT license](LICENSE).

---

## About Synetro

Fuse is built by [Synetro](https://synetro.dev). We build tools that make Laravel development faster, safer, and more enjoyable.

<div align="center">

[Website](https://synetro.dev) &nbsp;•&nbsp; [GitHub](https://github.com/synetro) &nbsp;•&nbsp; [Twitter](https://twitter.com/synetro)

</div>
