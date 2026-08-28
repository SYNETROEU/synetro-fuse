<?php

declare(strict_types=1);

namespace Synetro\Fuse\Usage;

use Illuminate\Contracts\Cache\Repository;

class UsageManager
{
    protected ?string $user = null;

    protected ?string $feature = null;

    protected ?string $quotaName = null;

    protected mixed $quotaOwner = null;

    protected ?int $limit = null;

    protected ?int $resetEvery = null;

    public function __construct(protected Repository $cache) {}

    public function for(string $user, string $feature): self
    {
        $this->user = $user;
        $this->feature = $feature;

        return $this;
    }

    public function quota(string $name): self
    {
        $this->quotaName = $name;

        return $this;
    }

    public function owner(mixed $owner): self
    {
        $this->quotaOwner = $owner;

        return $this;
    }

    public function limit(int $max): self
    {
        $this->limit = $max;

        return $this;
    }

    public function consume(int $amount = 1): bool
    {
        $key = $this->resolveKey();

        $used = $this->cache->get("fuse.usage.{$key}", 0);

        if ($this->limit !== null && ($used + $amount) > $this->limit) {
            return false;
        }

        $this->cache->put("fuse.usage.{$key}", $used + $amount, $this->resetEvery ?? 86400);

        return true;
    }

    public function usage(?int $resetEvery = null): int
    {
        $key = $this->resolveKey($resetEvery);

        return $this->cache->get("fuse.usage.{$key}", 0);
    }

    public function direct(string $name, mixed $owner, int $amount, ?int $resetEvery = null): bool
    {
        $key = "fuse.quota.{$name}.".(is_object($owner) ? get_class($owner) : $owner);

        $used = $this->cache->get("fuse.usage.{$key}", 0);

        $this->cache->put("fuse.usage.{$key}", $used + $amount, $resetEvery ?? 86400);

        return true;
    }

    protected function resolveKey(?int $resetEvery = null): string
    {
        $resetEvery = $resetEvery ?? $this->resetEvery;

        if ($this->quotaName !== null) {
            $owner = is_object($this->quotaOwner) ? get_class($this->quotaOwner) : ($this->quotaOwner ?? 'global');
            $key = "fuse.quota.{$this->quotaName}.{$owner}";
        } else {
            $key = ($this->user ?? 'global').'.'.($this->feature ?? 'default');
        }

        if ($resetEvery) {
            $period = now()->floor($resetEvery)->timestamp;

            return "{$key}.{$period}";
        }

        return $key;
    }
}
