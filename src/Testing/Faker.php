<?php

declare(strict_types=1);

namespace Synetro\Fuse\Testing;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Collection;

class Faker
{
    protected Factory $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public static function of(string $model): self
    {
        return new self(app('db')->connection()->getFactory());
    }

    public function count(int $count): Collection
    {
        $model = $this->resolveModel();

        return $model::factory()->count($count)->make();
    }

    public function one(): object
    {
        $model = $this->resolveModel();

        return $model::factory()->make();
    }

    protected function resolveModel(): string
    {
        return 'App\Models\User';
    }
}
