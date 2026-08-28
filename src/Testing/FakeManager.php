<?php

declare(strict_types=1);

namespace Synetro\Fuse\Testing;

class FakeManager
{
    protected array $fakes = [];

    public function register(string $class, callable $fake): void
    {
        $this->fakes[$class] = $fake;
    }

    public function restore(string $class): void
    {
        unset($this->fakes[$class]);
    }

    public function restoreAll(): void
    {
        $this->fakes = [];
    }
}
