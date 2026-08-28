<?php

declare(strict_types=1);

namespace Synetro\Fuse\Features;

class FeatureRollout
{
    public function __construct(
        protected Feature $feature,
        protected int $percentage,
    ) {}

    public function enabled(mixed $context = null): bool
    {
        $hash = hash('sha256', (string) $context);

        return hexdec(substr($hash, 0, 8)) % 100 < $this->percentage;
    }
}
