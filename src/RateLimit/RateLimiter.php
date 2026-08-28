<?php

declare(strict_types=1);

namespace Synetro\Fuse\RateLimit;

class RateLimiter
{
    protected ?string $name = null;
    protected int $maxAttempts = 5;
    protected int $decaySeconds = 60;

    public function __construct(protected \Illuminate\Contracts\Cache\Repository $cache) {}

    public function for(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function perMinute(int $max): self
    {
        $this->maxAttempts = $max;
        $this->decaySeconds = 60;

        return $this;
    }

    public function perSecond(int $max): self
    {
        $this->maxAttempts = $max;
        $this->decaySeconds = 1;

        return $this;
    }

    public function by(string $identifier): self
    {
        $this->name = ($this->name ?? 'default') . '.' . $identifier;

        return $this;
    }

    public function check(?string $key = null): bool
    {
        $key = $key ?? $this->name ?? 'default';

        $hits = $this->cache->add("fuse.rate_limit.{$key}", 0, $this->decaySeconds);

        if (!$hits) {
            $current = $this->cache->get("fuse.rate_limit.{$key}", 0);

            if ($current >= $this->maxAttempts) {
                return false;
            }

            $this->cache->increment("fuse.rate_limit.{$key}");
            return true;
        }

        $this->cache->increment("fuse.rate_limit.{$key}");
        return true;
    }
}
