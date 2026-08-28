<?php

declare(strict_types=1);

namespace Synetro\Fuse\Security;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SecurityManager
{
    public function __construct(protected Request $request) {}

    public function headers(): array
    {
        return config('fuse.security.headers', []);
    }

    public function check(): SecurityResult
    {
        $issues = [];

        if (config('app.debug')) {
            $issues[] = 'APP_DEBUG is enabled in production';
        }

        if (empty(config('app.key'))) {
            $issues[] = 'APP_KEY is not set';
        }

        return new SecurityResult($issues);
    }

    public function redact(string $value, int $visibleChars = 4): string
    {
        if (strlen($value) <= $visibleChars * 2) {
            return str_repeat('*', strlen($value));
        }

        return substr($value, 0, $visibleChars) . str_repeat('*', strlen($value) - ($visibleChars * 2)) . substr($value, -$visibleChars);
    }

    public function token(int $length = 32): string
    {
        return Str::random($length);
    }
}
