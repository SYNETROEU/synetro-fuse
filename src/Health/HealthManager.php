<?php

declare(strict_types=1);

namespace Synetro\Fuse\Health;

use Illuminate\Cache\CacheManager;
use Illuminate\Database\Connection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Queue\QueueManager;
use Synetro\Fuse\Health\Checks\HealthCheckInterface;

class HealthManager
{
    protected array $checks = [];

    protected array $results = [];

    public function __construct(
        protected Connection $db,
        protected CacheManager $cache,
        protected QueueManager $queue,
        protected Filesystem $files,
    ) {}

    public function register(string $name, HealthCheckInterface $check): self
    {
        $this->checks[$name] = $check;

        return $this;
    }

    public function check(string $name): HealthResult
    {
        if (!isset($this->checks[$name])) {
            throw new \InvalidArgumentException("Health check [{$name}] not found.");
        }

        try {
            $result = $this->checks[$name]->check();
        } catch (\Throwable $e) {
            $result = HealthResult::fail($name, $e->getMessage());
        }

        $this->results[$name] = $result;

        return $result;
    }

    public function all(): array
    {
        foreach (array_keys($this->checks) as $name) {
            $this->check($name);
        }

        return $this->results;
    }

    public function status(): string
    {
        $results = $this->all();

        if (collect($results)->contains(fn ($r) => $r->failed())) {
            return 'failed';
        }

        if (collect($results)->contains(fn ($r) => $r->degraded())) {
            return 'degraded';
        }

        return 'healthy';
    }
}
