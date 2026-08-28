<?php

declare(strict_types=1);

namespace Synetro\Fuse\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

abstract class Action
{
    use Dispatchable, Queueable, SerializesModels;

    abstract public function handle(mixed $payload): mixed;

    public static function run(mixed $payload): mixed
    {
        return (new static)->handle($payload);
    }

    public static function dispatch(mixed $payload): void
    {
        static::dispatch($payload);
    }

    public static function queue(mixed $payload): void
    {
        static::dispatch($payload);
    }

    public static function transaction(mixed $payload): mixed
    {
        return DB::transaction(fn () => static::run($payload));
    }

    public function authorize(mixed $user, ?string $ability = null): bool
    {
        $ability ??= 'execute';

        return app('auth')->user()?->can($ability, $this);
    }
}
