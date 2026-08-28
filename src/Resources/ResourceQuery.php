<?php

declare(strict_types=1);

namespace Synetro\Fuse\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ResourceQuery
{
    protected array $allowedFilters = [];

    protected array $allowedSorts = [];

    protected array $allowedFields = [];

    protected array $allowedIncludes = [];

    protected array $searchable = [];

    protected ?Builder $query = null;

    public function __construct(protected ResourceDefinition $resource)
    {
        $this->allowedFilters = $resource->filter();
        $this->allowedSorts = $resource->sort();
        $this->allowedFields = $resource->fields();
        $this->allowedIncludes = $resource->include();
        $this->searchable = $resource->search();

        $this->query = $this->resolveModel()::query();
    }

    public function search(string $query): self
    {
        if (! empty($this->searchable)) {
            $this->query->where(function (Builder $q) use ($query) {
                foreach ($this->searchable as $field) {
                    $q->orWhere($field, 'like', "%{$query}%");
                }
            });
        }

        return $this;
    }

    public function filter(array $filters): self
    {
        foreach ($filters as $key => $value) {
            if (! in_array($key, $this->allowedFilters, true)) {
                continue;
            }

            if (is_array($value)) {
                $this->query->whereIn($key, $value);
            } else {
                $this->query->where($key, $value);
            }
        }

        return $this;
    }

    public function sort(string $column, ?string $direction = null): self
    {
        $column = ltrim($column, '-');

        if (! in_array($column, $this->allowedSorts, true)) {
            return $this;
        }

        if (str_starts_with($column ?? '', '-') || $direction === 'desc') {
            $this->query->orderByDesc(ltrim($column, '-'));
        } else {
            $this->query->orderBy($column, $direction ?? 'asc');
        }

        return $this;
    }

    public function include(array $relations): self
    {
        $relations = array_intersect($relations, $this->allowedIncludes);

        $this->query->with($relations);

        return $this;
    }

    public function fields(array $fields): self
    {
        if (! empty($this->allowedFields)) {
            $fields = array_intersect($fields, $this->allowedFields);
        }

        $model = $this->resolveModel();

        $primaryKey = (new $model)->getKeyName();

        if (! in_array($primaryKey, $fields, true)) {
            $fields[] = $primaryKey;
        }

        $this->query->select($fields);

        return $this;
    }

    public function paginate(?int $perPage = null): LengthAwarePaginator
    {
        return $this->query->paginate($perPage ?? $this->resource->paginate());
    }

    public function getQuery(): Builder
    {
        return $this->query;
    }

    public function get(): Collection
    {
        return $this->query->get();
    }

    public function first(): ?Model
    {
        return $this->query->first();
    }

    public function apply(array $params): self
    {
        if (isset($params['search'])) {
            $this->search((string) $params['search']);
        }

        if (isset($params['filter']) && is_array($params['filter'])) {
            $this->filter($params['filter']);
        }

        if (isset($params['sort'])) {
            $this->sort((string) $params['sort']);
        }

        if (isset($params['include']) && is_array($params['include'])) {
            $this->include($params['include']);
        }

        if (isset($params['fields']) && is_array($params['fields'])) {
            $this->fields($params['fields']);
        }

        return $this;
    }

    protected function resolveModel(): string
    {
        return $this->resource->model();
    }
}
