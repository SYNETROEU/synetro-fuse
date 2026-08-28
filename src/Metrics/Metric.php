<?php

declare(strict_types=1);

namespace Synetro\Fuse\Metrics;

class Metric
{
    public function __construct(
        protected string $name,
        protected MetricsManager $manager,
    ) {}

    public function increment(int $amount = 1): void
    {
        $this->manager->record($this->name, $amount);
    }

    public function decrement(int $amount = 1): void
    {
        $this->manager->record($this->name, -$amount);
    }

    public function observe(mixed $value): void
    {
        $this->manager->record($this->name, $value);
    }

    public function value(): int
    {
        return $this->manager->get($this->name);
    }
}
