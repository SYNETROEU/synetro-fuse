<?php

declare(strict_types=1);

namespace Synetro\Fuse\Traits;

use Illuminate\Support\Collection;

trait HasCollections
{
    public function indexBy(string $key): Collection
    {
        return $this->keyBy($key);
    }

    public function pluckUnique(string $key): Collection
    {
        return $this->unique($key)->values();
    }

    public function groupByKey(string $key): Collection
    {
        return $this->groupBy($key);
    }
}
