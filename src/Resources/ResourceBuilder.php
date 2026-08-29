<?php

declare(strict_types=1);

namespace Synetro\Fuse\Resources;

class ResourceBuilder
{
    protected array $options = [
        'search' => [],
        'filter' => [],
        'sort' => [],
        'include' => [],
        'fields' => [],
        'paginate' => 15,
        'authorize' => false,
        'policy' => null,
        'middleware' => ['api'],
        'uri' => null,
        'controller' => null,
    ];

    public function __construct(
        protected string $model,
        protected ResourceManager $manager,
    ) {}

    public function search(array $fields): self
    {
        $this->options['search'] = $fields;

        return $this;
    }

    public function filter(array $fields): self
    {
        $this->options['filter'] = $fields;

        return $this;
    }

    public function sort(array $fields): self
    {
        $this->options['sort'] = $fields;

        return $this;
    }

    public function include(array $relations): self
    {
        $this->options['include'] = $relations;

        return $this;
    }

    public function fields(array $fields): self
    {
        $this->options['fields'] = $fields;

        return $this;
    }

    public function paginate(int $perPage = 15): self
    {
        $this->options['paginate'] = $perPage;

        return $this;
    }

    public function authorize(): self
    {
        $this->options['authorize'] = true;

        return $this;
    }

    public function policy(string $policy): self
    {
        $this->options['policy'] = $policy;

        return $this;
    }

    public function middleware(array $middleware): self
    {
        $this->options['middleware'] = $middleware;

        return $this;
    }

    public function uri(string $uri): self
    {
        $this->options['uri'] = $uri;

        return $this;
    }

    public function controller(string $controller): self
    {
        $this->options['controller'] = $controller;

        return $this;
    }

    public function register(): void
    {
        $resource = $this->build();

        $this->manager->register($resource);
    }

    public function buildQuery(): ResourceQuery
    {
        return new ResourceQuery($this->build());
    }

    public function build(): ResourceDefinition
    {
        $name = class_basename($this->model);

        return new ResourceDefinition(
            name: $name,
            model: $this->model,
            search: $this->options['search'],
            filter: $this->options['filter'],
            sort: $this->options['sort'],
            include: $this->options['include'],
            fields: $this->options['fields'],
            paginate: $this->options['paginate'],
            authorize: $this->options['authorize'],
            policy: $this->options['policy'],
            middleware: $this->options['middleware'],
            uri: $this->options['uri'],
            controller: $this->options['controller'],
        );
    }
}
