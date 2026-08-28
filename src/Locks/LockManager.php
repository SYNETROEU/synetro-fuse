<?php

declare(strict_types=1);

namespace Synetro\Fuse\Locks;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;

class LockManager
{
    protected ?string $name = null;

    protected int $timeout = 60;

    public function __construct(protected Repository $cache) {}

    public function for(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function timeout(int $seconds): self
    {
        $this->timeout = $seconds;

        return $this;
    }

    public function run(callable $callback): mixed
    {
        if ($this->name === null) {
            throw new \InvalidArgumentException('Name is required. Call for() first.');
        }

        $lock = Cache::lock("fuse.lock.{$this->name}", $this->timeout);

        if ($lock->get()) {
            try {
                return $callback();
            } finally {
                $lock->release();
            }
        }

        throw new \RuntimeException("Could not acquire lock: {$this->name}");
    }
}
