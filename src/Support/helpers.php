<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Queue;
use Synetro\Fuse\Actions\ActionResponse;
use Synetro\Fuse\Api\ResponseFactory;
use Synetro\Fuse\Locks\LockManager;
use Synetro\Fuse\Metrics\Metric;
use Synetro\Fuse\Notifications\NotificationManager;
use Synetro\Fuse\RateLimit\RateLimiter;
use Synetro\Fuse\Support\Facades\Fuse;
use Synetro\Fuse\Validation\Validator;

if (! function_exists('flow')) {
    function flow(string $action, mixed ...$args): ActionResponse
    {
        return Fuse::pipeline([$action])->run(...$args);
    }
}

if (! function_exists('api')) {
    function api(mixed $data = null, int $status = 200): ResponseFactory
    {
        return Fuse::api()->make($data, $status);
    }
}

if (! function_exists('authorize')) {
    function authorize(string $ability, mixed $arguments = []): mixed
    {
        return Fuse::auth()->authorize($ability, $arguments);
    }
}

if (! function_exists('can')) {
    function can(string $ability, mixed $arguments = []): bool
    {
        return Fuse::auth()->can($ability, $arguments);
    }
}

if (! function_exists('cannot')) {
    function cannot(string $ability, mixed $arguments = []): bool
    {
        return ! can($ability, $arguments);
    }
}

if (! function_exists('request_id')) {
    function request_id(): ?string
    {
        return request()->header('X-Request-ID') ?? request()->header('X-Fuse-Request-ID');
    }
}

if (! function_exists('background')) {
    function background(callable $callback, array $options = []): void
    {
        Queue::push($callback, $options);
    }
}

if (! function_exists('later')) {
    function later(int $seconds, callable $callback, array $options = []): void
    {
        Queue::later($seconds, $callback, $options);
    }
}

if (! function_exists('notify')) {
    function notify(mixed $notifiable, mixed $notification): NotificationManager
    {
        return Fuse::notify()->send($notifiable, $notification);
    }
}

if (! function_exists('today')) {
    function today(): CarbonImmutable
    {
        return CarbonImmutable::today();
    }
}

if (! function_exists('tomorrow')) {
    function tomorrow(): CarbonImmutable
    {
        return CarbonImmutable::tomorrow();
    }
}

if (! function_exists('yesterday')) {
    function yesterday(): CarbonImmutable
    {
        return CarbonImmutable::yesterday();
    }
}

if (! function_exists('daysAgo')) {
    function daysAgo(int $days): CarbonImmutable
    {
        return CarbonImmutable::now()->subDays($days);
    }
}

if (! function_exists('daysFromNow')) {
    function daysFromNow(int $days): CarbonImmutable
    {
        return CarbonImmutable::now()->addDays($days);
    }
}

if (! function_exists('metric')) {
    function metric(string $name): Metric
    {
        return Fuse::metrics()->metric($name);
    }
}

if (! function_exists('log_info')) {
    function log_info(string $message, array $context = []): void
    {
        Fuse::log()->info($message, $context);
    }
}

if (! function_exists('log_warning')) {
    function log_warning(string $message, array $context = []): void
    {
        Fuse::log()->warning($message, $context);
    }
}

if (! function_exists('log_error')) {
    function log_error(string $message, array $context = []): void
    {
        Fuse::log()->error($message, $context);
    }
}

if (! function_exists('validate')) {
    function validate(mixed $data, array $rules): Validator
    {
        return Fuse::validate($data, $rules);
    }
}

if (! function_exists('lock')) {
    function lock(string $name): LockManager
    {
        return Fuse::lock($name);
    }
}

if (! function_exists('limit')) {
    function limit(string $name): RateLimiter
    {
        return Fuse::limit($name);
    }
}
