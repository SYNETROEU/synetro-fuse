# Features

> Feature flags with global, user-targeted, and percentage rollout support.

## The Problem

Releasing features gradually requires conditional logic scattered across controllers and middleware.

## The Laravel Way

```php
if ($user->isBetaTester()) {
    // new dashboard
}

// Percentage rollout
if (rand(1, 100) <= 25) {
    // new checkout
}
```

## The Fuse Way

```php
use Synetro\Fuse\Support\Facades\Fuse;

// Global flag
if (Fuse::feature('new-dashboard')->enabled()) {
    // new dashboard
}

// Rollout percentage
if (Fuse::feature('new-checkout')->rollout(25)->enabled()) {
    // new checkout for 25% of users
}

// Direct check
if (Fuse::feature('new-checkout')->enabled($user)) {
    // new checkout for this user
}
```

## Installation / Setup

No setup required. Flags are read from `config/fuse.php` by default.

```php
'features' => [
    'new-dashboard' => true,
    'new-checkout' => false,
],
```

## Usage

```php
if (Fuse::feature('new-dashboard')->enabled()) {
    // ...
}

// With context
Fuse::feature('new-checkout')->enabled($user);
```

## Advanced Usage

```php
// Percentage rollout via FeatureRollout
$rollout = Fuse::feature('new-checkout')->rollout(25);
if ($rollout->enabled($user)) {
    // ...
}
```

## Security Considerations

- Feature flags are not a substitute for authorization.
- Cached flags may lag behind config changes.

## Testing

```php
FakeManager::register(FeatureManager::class, fn () => new FeatureManager(...));
```

## Laravel Equivalent

Custom feature flag system backed by `Config\Repository` and `Cache\Repository`.
