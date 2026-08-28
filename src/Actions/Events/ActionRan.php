<?php

declare(strict_types=1);

namespace Synetro\Fuse\Actions\Events;

class ActionRan
{
    public function __construct(
        public string $action,
        public mixed $payload,
        public mixed $result,
    ) {}
}
