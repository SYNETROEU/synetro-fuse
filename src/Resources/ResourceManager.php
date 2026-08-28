<?php

declare(strict_types=1);

namespace Synetro\Fuse\Resources;

class ResourceManager
{
    protected array $definitions = [];

    protected array $definitionsByUri = [];

    public function __construct(
        protected ResourceRouteRegistrar $routes,
    ) {}

    public function for(string $model): ResourceBuilder
    {
        return new ResourceBuilder($model, $this);
    }

    public function register(ResourceDefinition $resource): void
    {
        $name = $resource->name();
        $uri = $resource->uri();

        if (isset($this->definitions[$name])) {
            throw new \InvalidArgumentException("Resource [{$name}] is already registered.");
        }

        if (isset($this->definitionsByUri[$uri])) {
            throw new \InvalidArgumentException("Resource URI [{$uri}] is already registered.");
        }

        $this->definitions[$name] = $resource;
        $this->definitionsByUri[$uri] = $resource;

        if (config('fuse.routes.auto_register', true)) {
            $this->routes->register($resource);
        }
    }

    public function all(): array
    {
        return $this->definitions;
    }

    public function get(string $name): ?ResourceDefinition
    {
        return $this->definitions[$name] ?? null;
    }

    public function getByUri(string $uri): ?ResourceDefinition
    {
        return $this->definitionsByUri[$uri] ?? null;
    }
}
