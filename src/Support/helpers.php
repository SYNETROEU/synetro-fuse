<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Facade;

if (!function_exists('flow')) {
    function flow(string $action, mixed ...$args): \Synetro\Fuse\Actions\ActionResponse
    {
        return \Synetro\Fuse\Support\Facades\Fuse::pipeline([$action])->run(...$args);
    }
}

if (!function_exists('api')) {
    function api(mixed $data = null, int $status = 200): \Synetro\Fuse\Api\ResponseFactory
    {
        return \Synetro\Fuse\Support\Facades\Fuse::api()->make($data, $status);
    }
}

if (!function_exists('authorize')) {
    function authorize(string $ability, mixed $arguments = []): mixed
    {
        return \Synetro\Fuse\Support\Facades\Fuse::auth()->authorize($ability, $arguments);
    }
}

if (!function_exists('can')) {
    function can(string $ability, mixed $arguments = []): bool
    {
        return \Synetro\Fuse\Support\Facades\Fuse::auth()->can($ability, $arguments);
    }
}

if (!function_exists('cannot')) {
    function cannot(string $ability, mixed $arguments = []): bool
    {
        return !can($ability, $arguments);
    }
}

if (!function_exists('request_id')) {
    function request_id(): ?string
    {
        return request()->header('X-Request-ID') ?? request()->header('X-Fuse-Request-ID');
    }
}

if (!function_exists('background')) {
    function background(callable $callback, array $options = []): void
    {
        \Illuminate\Support\Facades\Queue::push($callback, $options);
    }
}

if (!function_exists('later')) {
    function later(int $seconds, callable $callback, array $options = []): void
    {
        \Illuminate\Support\Facades\Queue::later($seconds, $callback, $options);
    }
}

if (!function_exists('notify')) {
    function notify(mixed $notifiable, mixed $notification): \Synetro\Fuse\Notifications\NotificationManager
    {
        return \Synetro\Fuse\Support\Facades\Fuse::notify()->send($notifiable, $notification);
    }
}

if (!function_exists('today')) {
    function today(): \Carbon\CarbonImmutable
    {
        return \Carbon\CarbonImmutable::today();
    }
}

if (!function_exists('tomorrow')) {
    function tomorrow(): \Carbon\CarbonImmutable
    {
        return \Carbon\CarbonImmutable::tomorrow();
    }
}

if (!function_exists('yesterday')) {
    function yesterday(): \Carbon\CarbonImmutable
    {
        return \Carbon\CarbonImmutable::yesterday();
    }
}

if (!function_exists('daysAgo')) {
    function daysAgo(int $days): \Carbon\CarbonImmutable
    {
        return \Carbon\CarbonImmutable::now()->subDays($days);
    }
}

if (!function_exists('daysFromNow')) {
    function daysFromNow(int $days): \Carbon\CarbonImmutable
    {
        return \Carbon\CarbonImmutable::now()->addDays($days);
    }
}

if (!function_exists('metric')) {
    function metric(string $name): \Synetro\Fuse\Metrics\Metric
    {
        return \Synetro\Fuse\Support\Facades\Fuse::metrics()->metric($name);
    }
}

if (!function_exists('log_info')) {
    function log_info(string $message, array $context = []): void
    {
        \Synetro\Fuse\Support\Facades\Fuse::log()->info($message, $context);
    }
}

if (!function_exists('log_warning')) {
    function log_warning(string $message, array $context = []): void
    {
        \Synetro\Fuse\Support\Facades\Fuse::log()->warning($message, $context);
    }
}

if (!function_exists('log_error')) {
    function log_error(string $message, array $context = []): void
    {
        \Synetro\Fuse\Support\Facades\Fuse::log()->error($message, $context);
    }
}

if (!function_exists('validate')) {
    function validate(mixed $data, array $rules): \Synetro\Fuse\Validation\Validator
    {
        return \Synetro\Fuse\Support\Facades\Fuse::validate($data, $rules);
    }
}

if (!function_exists('lock')) {
    function lock(string $name): \Synetro\Fuse\Locks\LockManager
    {
        return \Synetro\Fuse\Support\Facades\Fuse::lock($name);
    }
}

if (!function_exists('limit')) {
    function limit(string $name): \Synetro\Fuse\RateLimit\RateLimiter
    {
        return \Synetro\Fuse\Support\Facades\Fuse::limit($name);
    }
}
