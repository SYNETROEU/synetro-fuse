# Validation

> Concise validation wrapper around Laravel's Validator with helper function support.

## The Problem

Every request needs rules, validation, and error inspection. Boilerplate spreads across controllers and Form Requests.

## The Laravel Way

```php
$validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
    'name' => 'required|string|max:255',
    'email' => 'required|email',
]);

$validator->validate();

$errors = $validator->errors();
```

## The Fuse Way

```php
use function Synetro\Fuse\Support\validate;

$validator = validate($data, [
    'name' => 'required|string|max:255',
    'email' => 'required|email',
]);

if ($validator->fails()) {
    // handle errors
}

$validator->validate();
$errors = $validator->errors();
```

## Installation / Setup

No setup required. The `validate()` helper is available after installing Fuse.

## Usage

```php
$validator = validate($request->all(), [
    'name' => 'required|string',
    'email' => 'required|email|unique:users,email',
]);

if ($validator->passes()) {
    $user = User::create($validator->validated());
}

$errors = $validator->errors();
```

## Advanced Usage

```php
// Reuse rules
$rules = [
    'name' => 'required|string',
    'email' => 'required|email',
];

$validator = validate($data, $rules);
$validator->validate();
```

## Security Considerations

- Always validate before mass-assignment.
- Do not pass raw request data to `validate()` when expecting specific fields.

## Testing

```php
$validator = validate(['name' => ''], ['name' => 'required']);
$this->assertTrue($validator->fails());
```

## Laravel Equivalent

Thin wrapper around `Illuminate\Support\Facades\Validator`.
