<?php

declare(strict_types=1);

namespace Synetro\Fuse\Database;

class DatabaseHealth
{
    public function __construct(
        public bool $connected,
        public string $database,
        public ?string $error = null,
    ) {}

    public static function pass(string $database = ''): self
    {
        return new self(true, $database);
    }

    public static function fail(string $error): self
    {
        return new self(false, '', $error);
    }

    public function passed(): bool
    {
        return $this->connected;
    }
}
