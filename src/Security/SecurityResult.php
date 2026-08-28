<?php

declare(strict_types=1);

namespace Synetro\Fuse\Security;

class SecurityResult
{
    public function __construct(
        public array $issues = [],
    ) {}

    public function passed(): bool
    {
        return empty($this->issues);
    }

    public function issues(): array
    {
        return $this->issues;
    }
}
