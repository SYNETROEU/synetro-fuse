<?php

declare(strict_types=1);

namespace Synetro\Fuse\Resources;

use Illuminate\Support\Facades\Gate;

class ResourcePolicy
{
    public function __construct(
        protected ?string $policyClass,
        protected bool $authorize,
    ) {}

    public function check(string $ability, mixed ...$arguments): void
    {
        if (! $this->authorize) {
            return;
        }

        Gate::authorize($ability, $arguments);
    }
}
