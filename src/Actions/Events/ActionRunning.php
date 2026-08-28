<?php

declare(strict_types=1);

namespace Synetro\Fuse\Actions\Events;

class ActionRunning
{
    public function __construct(
        public string $action,
        public mixed $payload,
    ) {}
}
