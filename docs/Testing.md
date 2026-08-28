# Testing

> Flow testing helpers, fake managers, and assertions for Fuse features.

## The Problem

Testing Fuse-powered endpoints requires repeating JSON requests and assertions for status codes, actions, webhooks, and notifications.

## The Laravel Way

```php
$response = $this->postJson('/users', $data);
$response->assertStatus(201);

// No built-in assertions for actions, webhooks, or notifications
```

## The Fuse Way

```php
use Synetro\Fuse\Testing\Flow;

Flow::fake();

Flow::post('/users', $data)->assertCreated();
Flow::assertActionRan(CreateUser::class);
Flow::assertWebhookSent('user.created');
Flow::assertNotificationSent($user, 'WelcomeNotification');
```

## Installation / Setup

All test cases should extend `Synetro\Fuse\Testing\TestCase`.

```php
class TestCase extends \Synetro\Fuse\Testing\TestCase
{
    // ...
}
```

## Usage

```php
Flow::fake();

Flow::post('/users', $data)->assertCreated();
Flow::get('/users/1')->assertOk();
Flow::put('/users/1', $data)->assertOk();
Flow::delete('/users/1')->assertNoContent();

Flow::assertActionRan(CreateUser::class);
Flow::assertWebhookSent('user.created');
Flow::assertNotificationSent($user, 'WelcomeNotification');
```

## Advanced Usage

```php
// Register a fake for a manager
FakeManager::register(WebhookManager::class, function () {
    return new class {
        public function for(string $name) { /* ... */ }
    };
});
```

## Security Considerations

- Fake managers should not execute real external HTTP calls.
- Ensure fakes are restored between tests using `FakeManager::restoreAll()`.

## Testing

```php
Flow::fake();
Flow::post('/users', ['name' => 'Alice'])->assertCreated();
Flow::assertActionRan(CreateUser::class);
```

## Laravel Equivalent

Extends Orchestra Testbench with `Flow` and `FakeManager` test utilities.
