<?php

declare(strict_types=1);

namespace Synetro\Fuse\Health;

class HealthResult
{
    public function __construct(
        public string $name,
        public string $status,
        public string $message,
        public array $meta = [],
    ) {}

    public static function pass(string $name, string $message = 'OK', array $meta = []): self
    {
        return new self($name, 'pass', $message, $meta);
    }

    public static function warn(string $name, string $message, array $meta = []): self
    {
        return new self($name, 'warn', $message, $meta);
    }

    public static function fail(string $name, string $message, array $meta = []): self
    {
        return new self($name, 'fail', $message, $meta);
    }

    public function passed(): bool
    {
        return $this->status === 'pass';
    }

    public function degraded(): bool
    {
        return $this->status === 'warn';
    }

    public function failed(): bool
    {
        return $this->status === 'fail';
    }
}
