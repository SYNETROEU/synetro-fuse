<?php

declare(strict_types=1);

namespace Synetro\Fuse\Metrics;

use Illuminate\Cache\Repository;
use Illuminate\Support\Collection;

class MetricsManager
{
    protected array $metrics = [];

    public function __construct(protected Repository $cache) {}

    public function metric(string $name): Metric
    {
        return new Metric($name, $this);
    }

    public function record(string $name, mixed $value = 1): void
    {
        $key = "fuse.metric.{$name}";
        $this->cache->increment($key, is_numeric($value) ? (int) $value : 1);
    }

    public function get(string $name): int
    {
        return $this->cache->get("fuse.metric.{$name}", 0);
    }

    public function all(): Collection
    {
        // Return all metric keys/values from cache
        return collect($this->metrics);
    }
}
