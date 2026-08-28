<?php

declare(strict_types=1);

namespace Synetro\Fuse\Features;

use Illuminate\Cache\Repository;
use Synetro\Fuse\Config\ConfigManager;

class FeatureManager
{
    public function __construct(
        protected ConfigManager $config,
        protected ?Repository $cache,
    ) {}

    public function for(string $key): Feature
    {
        return new Feature($key, $this);
    }

    public function enabled(string $key, mixed $context = null): bool
    {
        $feature = $this->for($key);

        return $feature->enabled($context);
    }

    public function cache(): bool
    {
        return config('fuse.features.cache', true);
    }

    public function ttl(): int
    {
        return config('fuse.features.ttl', 300);
    }
}
