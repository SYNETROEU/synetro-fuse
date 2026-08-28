<?php

declare(strict_types=1);

namespace Synetro\Fuse\Cache;

use Illuminate\Cache\Repository;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FuseCacheManager
{
    public function __construct(
        protected Repository $cache,
    ) {}

    public function remember(string $key, ?callable $callback = null, int $seconds = null): mixed
    {
        $seconds ??= config('fuse.cache.ttl', 3600);

        if ($callback) {
            return $this->cache->remember($key, $seconds, $callback);
        }

        return $this->cache->get($key);
    }

    public function rememberMany(array $keys, int $seconds = null): Collection
    {
        $seconds ??= config('fuse.cache.ttl', 3600);

        return $this->cache->many($keys)->map(fn ($v) => $v instanceof \Illuminate\Cache\CacheMiss ? null : $v);
    }

    public function forget(string $key): void
    {
        $this->cache->forget($key);
    }

    public function forgetMany(array $keys): void
    {
        foreach ($keys as $key) {
            $this->forget($key);
        }
    }

    public function tags(array $tags): self
    {
        $this->cache = $this->cache->tags($tags);

        return $this;
    }
}
