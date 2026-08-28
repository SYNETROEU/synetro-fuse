# Api

> Consistent JSON response envelope, pagination metadata, and machine-readable error format.

## The Problem

Controllers repeatedly format JSON responses, pagination metadata, and error envelopes inconsistently.

## The Laravel Way

```php
return response()->json([
    'data' => $users,
    'meta' => [
        'total' => $users->total(),
        'per_page' => $users->perPage(),
    ],
], 200);

return response()->json([
    'error' => [
        'code' => 'USER_NOT_FOUND',
        'message' => 'User does not exist.',
    ],
], 404);
```

## The Fuse Way

```php
use Synetro\Fuse\Support\Facades\Fuse;

return Fuse::api()->created($user);
return Fuse::api()->updated($user);
return Fuse::api()->deleted();

return api()->error('USER_NOT_FOUND', 'User does not exist.');
```

## Installation / Setup

No setup required. `ResponseFactory` is returned by `Fuse::api()->make()`.

## Usage

```php
return Fuse::api()->make($data, 200)->respond();
return Fuse::api()->created($user);
return Fuse::api()->updated($user);
return Fuse::api()->deleted();
return Fuse::api()->error('USER_NOT_FOUND', 'User does not exist.', 404, ['id' => 1]);
```

## Advanced Usage

```php
$response = api()->make($data)
    ->withMeta(['request_id' => request_id()])
    ->respond();
```

## Security Considerations

- Do not expose internal exception messages via `error()`.
- Keep error codes stable for client handling.

## Testing

```php
Flow::post('/users', $data)->assertCreated();
Flow::get('/users/999')->assertNotFound();
```

## Laravel Equivalent

Wraps `response()->json()` with a consistent envelope and helper shortcuts.
