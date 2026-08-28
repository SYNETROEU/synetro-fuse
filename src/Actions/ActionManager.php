<?php

declare(strict_types=1);

namespace Synetro\Fuse\Actions;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;

class ActionManager
{
    public function __construct(
        protected \Illuminate\Events\Dispatcher $events,
        protected \Illuminate\Contracts\Queue\Queue $queue,
    ) {}

    public function run(string $action, mixed $payload): mixed
    {
        $instance = new $action();

        $this->events->dispatch(new Events\ActionRunning($action, $payload));

        $result = $instance->handle($payload);

        $this->events->dispatch(new Events\ActionRan($action, $payload, $result));

        return $result;
    }

    public function queue(string $action, mixed $payload): void
    {
        if (is_subclass_of($action, ShouldQueue::class)) {
            $action::dispatch($payload);
        }
    }
}
