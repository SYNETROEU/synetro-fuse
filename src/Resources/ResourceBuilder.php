<?php

declare(strict_types=1);

namespace Synetro\Fuse\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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
        'validate' => [],
        'policy' => null,
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

    public function register(): void
    {
        $this->manager->register($this->model, $this->options);
    }

    public function buildQuery(): FuseQuery
    {
        return new FuseQuery($this->model, $this->options);
    }
}
