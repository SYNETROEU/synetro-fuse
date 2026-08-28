<?php

declare(strict_types=1);

namespace Synetro\Fuse\Idempotency;

use Illuminate\Contracts\Cache\Repository;

class IdempotencyManager
{
    protected ?string $key = null;

    protected ?int $ttl = null;

    public function __construct(protected Repository $cache) {}

    public function for(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function ttl(int $seconds): self
    {
        $this->ttl = $seconds;

        return $this;
    }

    public function run(callable $callback): mixed
    {
        if ($this->key === null) {
            throw new \InvalidArgumentException('Key is required. Call for() first.');
        }

        $cached = $this->cache->get("fuse.idempotent.{$this->key}");

        if ($cached !== null) {
            return $cached;
        }

        $result = $callback();

        $this->cache->put("fuse.idempotent.{$this->key}", $result, $this->ttl ?? 3600);

        return $result;
    }
}
