<?php

declare(strict_types=1);

namespace Synetro\Fuse\Resources;

use Illuminate\Support\Str;

class ResourceDefinition
{
    public function __construct(
        protected string $name,
        protected string $model,
        protected array $search = [],
        protected array $filter = [],
        protected array $sort = [],
        protected array $include = [],
        protected array $fields = [],
        protected int $paginate = 15,
        protected bool $authorize = false,
        protected ?string $policy = null,
        protected array $middleware = ['api'],
        protected ?string $uri = null,
        protected ?string $controller = null,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function model(): string
    {
        return $this->model;
    }

    public function search(): array
    {
        return $this->search;
    }

    public function filter(): array
    {
        return $this->filter;
    }

    public function sort(): array
    {
        return $this->sort;
    }

    public function include(): array
    {
        return $this->include;
    }

    public function fields(): array
    {
        return $this->fields;
    }

    public function paginate(): int
    {
        return $this->paginate;
    }

    public function authorize(): bool
    {
        return $this->authorize;
    }

    public function policy(): ?string
    {
        return $this->policy;
    }

    public function middleware(): array
    {
        return $this->middleware;
    }

    public function uri(): string
    {
        return $this->uri ?? Str::plural(Str::kebab($this->name));
    }

    public function controller(): ?string
    {
        return $this->controller;
    }

    public function parameter(): string
    {
        return Str::kebab(class_basename($this->model));
    }
}
