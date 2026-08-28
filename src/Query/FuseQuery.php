<?php

declare(strict_types=1);

namespace Synetro\Fuse\Query;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Synetro\Fuse\Exceptions\QueryException;

class FuseQuery
{
    protected array $allowedFilters = [];
    protected array $allowedSorts = [];
    protected array $allowedFields = [];
    protected array $allowedIncludes = [];
    protected array $searchable = [];

    public function __construct(
        protected string $model,
        protected array $options = [],
    ) {
        $this->allowedFilters = $options['filter'] ?? [];
        $this->allowedSorts = $options['sort'] ?? [];
        $this->allowedFields = $options['fields'] ?? [];
        $this->allowedIncludes = $options['include'] ?? [];
        $this->searchable = $options['search'] ?? [];
    }

    public function search(string $query): self
    {
        if (!empty($this->searchable)) {
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
            if (!in_array($key, $this->allowedFilters, true)) {
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
        if (!in_array(ltrim($column, '-'), $this->allowedSorts, true)) {
            return $this;
        }

        if (str_starts_with($column, '-')) {
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
        if (!empty($this->allowedFields)) {
            $fields = array_intersect($fields, $this->allowedFields);
        }

        $this->query->select($fields);

        return $this;
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query->paginate($perPage);
    }

    public function get(): Collection
    {
        return $this->query->get();
    }

    public function first(): ?Model
    {
        return $this->query->first();
    }

    public function count(): int
    {
        return $this->query->count();
    }

    public function apply(array $params): self
    {
        if (isset($params['search'])) {
            $this->search($params['search']);
        }

        if (isset($params['filter'])) {
            $this->filter($params['filter']);
        }

        if (isset($params['sort'])) {
            $this->sort($params['sort']);
        }

        if (isset($params['include'])) {
            $this->include($params['include']);
        }

        if (isset($params['fields'])) {
            $this->fields($params['fields']);
        }

        return $this;
    }
}
