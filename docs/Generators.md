# Generators

> Code generators for models, controllers, actions, tests, and full CRUD stacks.

## The Problem

Scaffolding a new feature requires creating multiple files (model, controller, tests, actions) with consistent structure.

## The Laravel Way

```bash
php artisan make:model Product -mcr
# Then manually create Form Requests, Policies, Tests, etc.
```

## The Fuse Way

```bash
php artisan fuse:make Product
php artisan fuse:make Product --full
```

## Installation / Setup

Run `php artisan fuse:install` to register the `fuse:make` command.

```bash
php artisan fuse:make Product
php artisan fuse:make Product --full
```

## Usage

```bash
# Generate a model with migration
php artisan fuse:make Product

# Generate full CRUD stack
php artisan fuse:make Product --full
```

## Advanced Usage

```bash
# Inspect a model
php artisan fuse:inspect Product

# Generate OpenAPI spec
php artisan fuse:openapi
```

## Security Considerations

- Generated code should be reviewed before deployment.
- Do not run generators in production.

## Testing

```bash
php artisan fuse:make Product --full
# Verify files exist
test -f app/Models/Product.php
test -f app/Http/Controllers/ProductController.php
```

## Laravel Equivalent

Extends `make:model`, `make:controller`, and custom stub generators.
