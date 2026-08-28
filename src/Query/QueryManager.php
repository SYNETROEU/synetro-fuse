<?php

declare(strict_types=1);

namespace Synetro\Fuse\Query;

class QueryManager
{
    protected array $filters = [];

    public function for(string $model): FuseQuery
    {
        return new FuseQuery($model);
    }

    public function filter(string $name, callable $callback): void
    {
        $this->filters[$name] = $callback;
    }

    public function apply(string $name, \Illuminate\Database\Eloquent\Builder $query): void
    {
        if (isset($this->filters[$name])) {
            ($this->filters[$name])($query);
        }
    }
}
