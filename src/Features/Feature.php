<?php

declare(strict_types=1);

namespace Synetro\Fuse\Features;

use Illuminate\Database\Connection;

class Feature
{
    public function __construct(
        protected string $key,
        protected FeatureManager $manager,
    ) {}

    public function enabled(mixed $context = null): bool
    {
        $cacheKey = "fuse.feature.{$this->key}." . ($context ? md5(serialize($context)) : 'global');

        if ($this->manager->cache()) {
            return cache()->remember($cacheKey, $this->manager->ttl(), function () use ($context) {
                return $this->check($context);
            });
        }

        return $this->check($context);
    }

    public function rollout(int $percentage): FeatureRollout
    {
        return new FeatureRollout($this, $percentage);
    }

    public function for(mixed $context): Feature
    {
        return new self($this->key, $this->manager);
    }

    protected function check(mixed $context): bool
    {
        $enabled = config("fuse.features.{$this->key}");

        if (is_bool($enabled)) {
            return $enabled;
        }

        return false;
    }
}
