<div align="center">

# Fuse

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square\&logo=php)
![Laravel](https://img.shields.io/badge/Laravel-11%2B-FF2D20?style=flat-square\&logo=laravel)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)
![Status](https://img.shields.io/badge/Status-Active-success?style=flat-square)

**Powerful Laravel applications. Without the boilerplate.**

[Installation](#installation)  •  [Quick Start](#quick-start)  •  [Features](#features)  •  [Documentation](#documentation)  •  [Testing](#testing)  •  [Contributing](#contributing)

</div>

---

## What is Fuse?

Fuse is an open-source developer productivity layer for Laravel. It provides reusable application infrastructure for common tasks such as CRUD resources, querying, configuration, secrets, feature flags, webhooks, health checks, API responses, actions, pipelines, auditing, caching, idempotency, rate limiting, quotas, and more.

Its purpose is simple:

> **Eliminate repetitive Laravel glue code while keeping Laravel itself intact.**

Instead of manually wiring controllers, form requests, resources, policies, filters, routes, and tests for every entity, Fuse lets you write:

```php
use Synetro\Fuse\Support\Facades\Fuse;

Fuse::resource(Product::class);
```

And still use normal Laravel whenever you need it:

```php
Product::query();

DB::transaction(...);

Route::get(...);

Cache::remember(...);

Mail::to(...);
```

Fuse is designed to complement Laravel rather than replace it.

### Built by Synetro

Fuse is maintained by the team behind [Synetro](https://synetro.eu), a commercial Docker-based infrastructure and hosting control panel for developers and hosting providers.

---

## Installation

Install Fuse through Composer:

```bash
composer require synetro/fuse
```

### Requirements

* PHP 8.2 or higher
* Laravel 11.x, 12.x, or 13.x
* OpenSSL PHP extension

### Setup

Run the installation command:

```bash
php artisan fuse:install
```

This will:

* Publish the Fuse configuration
* Publish database migrations
* Register optional middleware
* Verify package requirements

### Verify Installation

Run the Fuse diagnostic command:

```bash
php artisan fuse:doctor
```

---

## Quick Start

### Resources

Register a resource with CRUD operations, searching, filtering, sorting, includes, fields, pagination, and authorization:

```php
use Synetro\Fuse\Support\Facades\Fuse;

Fuse::resource(Product::class)
    ->search(['name', 'sku'])
    ->filter(['status', 'category_id'])
    ->sort(['name', 'created_at'])
    ->include(['category'])
    ->fields(['id', 'name', 'price'])
    ->paginate(25)
    ->authorize();
```

### Configuration

Use database-backed, cached configuration with typed access:

```php
Fuse::config('billing.currency');

Fuse::config()->set('billing.currency', 'EUR');

Fuse::config('billing.max_attempts')->int();

Fuse::config('company')->array();
```

### Feature Flags

Create and evaluate feature flags:

```php
Fuse::feature('new-dashboard')->enabled();

Fuse::feature('new-checkout')
    ->rollout(25)
    ->enabled();
```

### Secrets

Store encrypted application secrets with log redaction:

```php
Fuse::secret('stripe.secret')->get();

Fuse::secret('stripe.secret')->set($secret);
```

### Cache Helpers

Simplify common caching operations:

```php
Fuse::cache('users', fn () => User::all());

Fuse::cacheFor('expensive-data', 600, fn () => ...);

Fuse::forget('users');
```

### Health Checks

Inspect application health:

```php
Fuse::health()->status();

Fuse::health()->check('database');
```

### Webhooks

Send signed, queued webhooks with retries and timeouts:

```php
Webhook::send($url, 'invoice.created', $invoice)
    ->signed()
    ->queued()
    ->retry(5)
    ->timeout(10);
```

### Actions

Create reusable application actions:

```php
class CreateOrder extends Action
{
    public function handle(array $data)
    {
        return Order::create($data);
    }
}
```

Run actions directly, transactionally, or through a queue:

```php
CreateOrder::run($data);

CreateOrder::transaction($data);

CreateOrder::queue($data);
```

### Pipelines

Compose application workflows:

```php
Fuse::pipeline([
    ValidateOrder::class,
    CalculatePrice::class,
    ChargeCustomer::class,
    CreateOrder::class,
])->run($order);
```

### API Responses

Return consistent API responses:

```php
return api()->created($user);

return api()->updated($user);

return api()->deleted();

return api()->error(
    'USER_NOT_FOUND',
    'User does not exist.'
);
```

### Logging

Create structured application logs:

```php
Fuse::log()
    ->event('invoice.created')
    ->user($user)
    ->context($invoice)
    ->write();

log_info('Order created', [
    'id' => $order->id,
]);

log_warning('Low stock', [
    'sku' => $product->sku,
]);

log_error('Payment failed', [
    'order_id' => $order->id,
]);
```

### Metrics

Record counters and observations:

```php
metric('orders.created')->increment();

metric('payment.amount')->observe($amount);
```

### Background Tasks

Run background or delayed operations:

```php
background(fn () => expensiveOperation());

later(300, fn () => sendReminder());
```

### Notifications

Route notifications through multiple channels:

```php
notify($user, 'Welcome!');

notify($user)
    ->email()
    ->database()
    ->broadcast();
```

### File Attachments

Attach and retrieve files through Laravel's filesystem:

```php
$user->attachFile('avatar', $uploadedFile);

$user->file('avatar')->url();

$user->file('avatar')
    ->temporaryUrl(now()->addHours(2));
```

### Security

Use security helpers and diagnostics:

```php
Fuse::security()->headers();

Fuse::security()->check();

Fuse::security()->redact($secret);
```

### Query System

Build explicit, allow-listed queries:

```php
Product::fuse()
    ->search('keyboard')
    ->filter(['status' => 'active'])
    ->sort('-created_at')
    ->include(['category'])
    ->fields(['id', 'name', 'price'])
    ->paginate();
```

### Validation

Use concise validation backed by Laravel's validator:

```php
Fuse::validate($data, [
    'name' => 'required|string',
    'email' => 'required|email',
]);
```

### Bulk Operations

Perform bulk operations:

```php
Fuse::bulk($products)
    ->update(['status' => 'archived']);
```

### Import / Export

Import and export application data:

```php
Fuse::import(Product::class, $file);

Fuse::export(Product::class)
    ->format('csv')
    ->download();
```

### Idempotency

Protect operations from duplicate execution:

```php
Fuse::idempotent($request)
    ->run(fn () => CreateOrder::run($data));
```

### Distributed Locks

Coordinate operations across processes:

```php
Fuse::lock("payment:{$payment->id}")
    ->run(fn () => processPayment($payment));
```

### Rate Limiting

Use fluent Laravel rate limiting:

```php
Fuse::limit('login')
    ->perMinute(5)
    ->by($request->ip())
    ->check();
```

### Usage and Quotas

Track resource consumption and limits:

```php
Fuse::usage($user, 'projects')
    ->limit(10)
    ->consume();

Fuse::quota('storage')
    ->for($tenant)
    ->consume(500);
```

### Query Profiling

Profile expensive operations:

```php
Fuse::profile(
    fn () => expensiveOperation()
);
```

### Reusable Filters

Register reusable query filters:

```php
Fuse::filter(
    'active',
    fn ($query) => $query->where('status', 'active')
);

Product::fuse()
    ->filter('active')
    ->get();
```

### Additional Commands

Fuse also provides commands for:

```bash
php artisan fuse:security
php artisan fuse:auth
php artisan fuse:cleanup
```

---

## Features

| Feature               | Description                                                                                             |
| --------------------- | ------------------------------------------------------------------------------------------------------- |
| **Resources / CRUD**  | Resource registration with search, filtering, sorting, pagination, and authorization                    |
| **Query System**      | Explicit query building with allow-lists for search, filter, sort, include, and fields                  |
| **Bulk Operations**   | Bulk update and delete operations with transactions, chunking, and authorization                        |
| **Import / Export**   | CSV and JSON import/export with chunking, validation, and failed-row reports                            |
| **Actions**           | Reusable application actions with transaction and queue support                                         |
| **Pipelines**         | Synchronous and queued workflow orchestration                                                           |
| **API Responses**     | Consistent JSON envelopes, pagination metadata, and machine-readable errors                             |
| **Validation**        | Concise validation helpers backed by Laravel's Validator                                                |
| **DB Configuration**  | Database-backed configuration with caching, typed access, and environment overrides                     |
| **Secrets**           | Encrypted application secrets with log redaction and rotation support                                   |
| **Feature Flags**     | Global, user-targeted, and percentage-based feature rollouts                                            |
| **Multi-Tenancy**     | Tenant-aware configuration and feature flags                                                            |
| **Caching**           | Simple cache helpers with invalidation support                                                          |
| **Idempotency**       | First-class idempotency support for distributed operations and payments                                 |
| **Distributed Locks** | Atomic lock wrappers with timeout, blocking, and owner support                                          |
| **Rate Limiting**     | Fluent rate limiting integrated with Laravel RateLimiter                                                |
| **Usage / Quota**     | Usage tracking, quotas, and entitlement management                                                      |
| **Webhooks**          | Outgoing webhooks with HMAC signatures, retries, queues, and timeouts                                   |
| **Audit Logging**     | Model auditing with sensitive-field exclusion                                                           |
| **Health Checks**     | Database, cache, queue, and storage health monitoring                                                   |
| **Security**          | Security headers, diagnostics, and sensitive-data redaction                                             |
| **Metrics**           | Counter and observation metrics                                                                         |
| **Files**             | File attachment helpers built on Laravel Filesystem                                                     |
| **Notifications**     | Simple notification routing                                                                             |
| **Logging**           | Structured logging with request context                                                                 |
| **Testing**           | Flow testing helpers, fake managers, and assertions                                                     |
| **Generators**        | Code generation for models, controllers, actions, tests, and CRUD                                       |
| **OpenAPI**           | Automatic OpenAPI documentation generation                                                              |
| **Artisan Commands**  | Installation, diagnostics, security, authentication, cleanup, generation, inspection, OpenAPI, and more |

---

## Documentation

### Core Concepts

* [Resources & CRUD](docs/Resources.md)
* [Query System](docs/Query.md)
* [Bulk Operations](docs/Bulk.md)
* [Import / Export](docs/ImportExport.md)
* [Actions & Pipelines](docs/Actions.md)
* [API Responses](docs/Api.md)
* [Validation](docs/Validation.md)
* [Configuration](docs/Configuration.md)
* [Secrets](docs/Secrets.md)
* [Feature Flags](docs/Features.md)
* [Tenancy](docs/Tenancy.md)
* [Caching](docs/Caching.md)
* [Idempotency](docs/Idempotency.md)
* [Distributed Locks](docs/Locks.md)
* [Rate Limiting](docs/RateLimit.md)
* [Usage & Quotas](docs/Usage.md)
* [Webhooks](docs/Webhooks.md)
* [Audit Logging](docs/Audit.md)
* [Health Checks](docs/Health.md)
* [Security](docs/Security.md)
* [Metrics](docs/Metrics.md)
* [Testing](docs/Testing.md)
* [Generators](docs/Generators.md)
* [Extending Fuse](docs/Extending.md)

---

## Artisan Commands

```bash
php artisan fuse:install       # Install the package
php artisan fuse:doctor        # Run diagnostics
php artisan fuse:security      # Run security diagnostics
php artisan fuse:optimize      # Optimize caches
php artisan fuse:make Product  # Generate a component
php artisan fuse:make Product --full
                               # Generate full CRUD
php artisan fuse:auth          # Scaffold authentication
php artisan fuse:cleanup       # Clean up expired data
php artisan fuse:about         # Display application information
php artisan fuse:routes        # List Fuse routes
php artisan fuse:models        # List Fuse models
php artisan fuse:health        # Run health checks
php artisan fuse:openapi       # Generate an OpenAPI specification
php artisan fuse:docs          # Generate documentation
php artisan fuse:inspect User  # Inspect a model
```

---

## Configuration

Publish the Fuse configuration:

```bash
php artisan vendor:publish --tag=fuse-config
```

A typical configuration looks like:

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

Fuse includes testing helpers for application flows, actions, webhooks, and other functionality.

```php
use Synetro\Fuse\Testing\Flow;

Flow::fake();

Flow::post('/users', $data)
    ->assertCreated();

Flow::assertActionRan(CreateUser::class);

Flow::assertWebhookSent('user.created');
```

### Running Tests

```bash
vendor/bin/phpunit
```

---

## Security

Fuse is designed with security-conscious defaults and application diagnostics.

If you discover a security vulnerability:

1. **Do not** open a public GitHub issue.
2. Report the vulnerability through the project's security contact.
3. Allow reasonable time for the issue to be investigated and patched before public disclosure.

### Security Features

* Secrets are encrypted at rest using Laravel's encrypter
* Secrets can be automatically redacted from logs
* Webhook signatures use HMAC-SHA256
* Webhook replay attacks can be mitigated through timestamp validation
* Query filters use explicit allow-lists
* API-exposed model fields must be explicitly configured
* Audit logs exclude sensitive fields by default
* Security headers are configurable
* `php artisan fuse:security` can diagnose common application security configuration issues including:

  * `APP_KEY`
  * `APP_DEBUG`
  * HTTPS
  * Cookies
  * CSRF
  * CORS
  * Other application security settings

---

## Contributing

Contributions are welcome.

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for contribution guidelines.

### Development Setup

Clone the repository:

```bash
git clone https://github.com/SYNETROEU/synetro-fuse.git

cd synetro-fuse

composer install
```

Run the test suite:

```bash
vendor/bin/phpunit
```

### Code Style

Fuse uses Laravel Pint for code formatting:

```bash
vendor/bin/pint
```

---

## Roadmap

### Completed

* [x] Package skeleton, service provider, facade, and helpers
* [x] Configuration system
* [x] Installation command
* [x] Diagnostic commands
* [x] Database migrations
* [x] Database-backed configuration
* [x] Configuration caching
* [x] Secrets manager
* [x] Secret encryption and log redaction
* [x] Feature flags
* [x] Feature flag rollouts
* [x] CRUD Resource system
* [x] Query system with allow-lists
* [x] Actions
* [x] Pipelines
* [x] API response layer
* [x] Health checks
* [x] Security manager
* [x] Security diagnostics
* [x] Logging
* [x] Metrics
* [x] Notifications
* [x] Webhooks
* [x] Webhook signing and retries
* [x] Audit logging
* [x] Caching helpers
* [x] File attachment helpers
* [x] Testing helpers
* [x] Code generators
* [x] OpenAPI generation
* [x] Application inspection commands
* [x] Validation helpers
* [x] Bulk operations
* [x] Import / Export
* [x] Idempotency
* [x] Distributed locks
* [x] Rate limiting
* [x] Usage and quota tracking
* [x] Query profiler
* [x] Auto-discovery
* [x] Authentication scaffolding
* [x] Maintenance and cleanup commands

---

## Philosophy

Fuse does not aim to replace Laravel.

It exists to remove repetitive application glue while preserving Laravel's conventions and escape hatches.

Every feature should answer one question:

> **What annoying boilerplate does this eliminate?**

Features that merely rename or wrap existing Laravel APIs without providing meaningful value do not belong in Fuse.

---

## License

Fuse is open-source software licensed under the [MIT License](LICENSE).

---

## About Synetro

[Synetro](https://synetro.eu) is a commercial Docker-based infrastructure and hosting control panel for developers, SaaS companies, agencies, hosting providers, and businesses operating applications on Linux infrastructure.

Fuse is an open-source library maintained as part of the Synetro ecosystem.
