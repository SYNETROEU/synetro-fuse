<?php

declare(strict_types=1);

namespace Synetro\Fuse\Support;

use Illuminate\Contracts\Container\Container;
use Synetro\Fuse\Health\HealthCheckInterface;
use Synetro\Fuse\Discovery\DiscoveryManager;

class FuseExtensionManager
{
    protected array $extensions = [];
    protected array $macros = [];
    protected array $healthChecks = [];
    protected array $discoveryClasses = [];
    protected array $generatorStubs = [];
    protected array $eventSubscribers = [];

    public function __construct(protected Container $app) {}

    public function extend(string $name, callable $factory): void
    {
        $this->extensions[$name] = $factory;
    }

    public function macro(string $name, callable $macro): void
    {
        $this->macros[$name] = $macro;
    }

    public function has(string $name): bool
    {
        return isset($this->extensions[$name]) || isset($this->macros[$name]);
    }

    public function get(string $name): ?callable
    {
        return $this->macros[$name] ?? $this->extensions[$name] ?? null;
    }

    public function all(): array
    {
        return array_merge($this->extensions, $this->macros);
    }

    public function registerHealthCheck(string $name, HealthCheckInterface $check): void
    {
        $this->healthChecks[$name] = $check;
    }

    public function healthChecks(): array
    {
        return $this->healthChecks;
    }

    public function registerDiscovery(string $type, string $class): void
    {
        $this->discoveryClasses[$type][] = $class;
    }

    public function discoveryClasses(string $type = null): array
    {
        if ($type) {
            return $this->discoveryClasses[$type] ?? [];
        }

        return $this->discoveryClasses;
    }

    public function registerGeneratorStub(string $component, string $path): void
    {
        $this->generatorStubs[$component] = $path;
    }

    public function generatorStubs(): array
    {
        return $this->generatorStubs;
    }

    public function subscribe(string $event, callable $listener): void
    {
        $this->eventSubscribers[$event][] = $listener;
    }

    public function eventSubscribers(): array
    {
        return $this->eventSubscribers;
    }
}
