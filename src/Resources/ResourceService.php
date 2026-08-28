<?php

declare(strict_types=1);

namespace Synetro\Fuse\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class ResourceService
{
    public function find(string $model, mixed $id): Model
    {
        return $model::findOrFail($id);
    }

    public function paginate(string $model, ResourceQuery $query, ?int $perPage = null): LengthAwarePaginator
    {
        return $query->paginate($perPage ?? 15);
    }

    public function create(string $model, array $data): Model
    {
        $instance = new $model;

        $instance->fill($data);
        $instance->save();

        return $instance;
    }

    public function update(string $model, mixed $id, array $data): Model
    {
        $instance = $this->find($model, $id);

        $instance->fill($data);
        $instance->save();

        return $instance;
    }

    public function delete(string $model, mixed $id): void
    {
        $instance = $this->find($model, $id);

        $instance->delete();
    }
}
