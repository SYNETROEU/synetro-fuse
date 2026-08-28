<?php

declare(strict_types=1);

namespace Synetro\Fuse\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

class AuthManager
{
    public function authorize(string $ability, mixed $arguments = []): mixed
    {
        return app('auth')->user()?->can($ability, $arguments) ?? false;
    }

    public function can(string $ability, mixed $arguments = []): bool
    {
        return app('auth')->user()?->can($ability, $arguments) ?? false;
    }

    public function user(): ?Authenticatable
    {
        return app('auth')->user();
    }

    public function check(): bool
    {
        return app('auth')->check();
    }
}
