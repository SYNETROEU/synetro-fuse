# Secrets

> Encrypted secrets management with log redaction and rotation support.

## The Problem

API keys and credentials often end up in `.env`, config files, or logs. Rotating them requires coordinated deploys and risks leaking plaintext.

## The Laravel Way

```php
$secret = env('STRIPE_SECRET');
// or
$secret = config('services.stripe.secret');

// Logging accidentally leaks the value
logger()->info('Stripe secret', ['secret' => $secret]);
```

## The Fuse Way

```php
use Synetro\Fuse\Support\Facades\Fuse;

// Read
$stripeSecret = Fuse::secret('stripe.secret')->get();

// Write
Fuse::secret('stripe.secret')->set($newSecret);

// Delete
Fuse::secret('stripe.secret')->delete();

// Redact for logs
$redacted = Fuse::security()->redact($stripeSecret);
```

## Installation / Setup

Run `php artisan fuse:install`. Secrets are stored encrypted at `storage/app/fuse/secrets/` using Laravel's encrypter.

```php
'secrets' => [
    'encryption' => true,
    'redact_from_logs' => true,
    'cache' => true,
],
```

## Usage

```php
$secret = Fuse::secret('stripe.secret')->get();

if ($secret === null) {
    Fuse::secret('stripe.secret')->set($secretFromVault);
}
```

## Advanced Usage

```php
// Redact a value manually
$safe = Fuse::secret('stripe.secret')->redact($value);
```

## Security Considerations

- Values are encrypted at rest using `APP_KEY`.
- The `storage/app/fuse/secrets/` directory must not be publicly accessible.
- Never log raw secret values.

## Testing

```php
Fuse::secret('test.key')->set('value');
$this->assertEquals('value', Fuse::secret('test.key')->get());
```

## Laravel Equivalent

Custom encrypted file store backed by `Illuminate\Encryption\Encrypter`.
