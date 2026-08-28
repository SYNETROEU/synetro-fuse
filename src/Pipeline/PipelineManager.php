<?php

declare(strict_types=1);

namespace Synetro\Fuse\Pipeline;

use Illuminate\Support\Collection;
use Illuminate\Pipeline\Pipeline as LaravelPipeline;

class PipelineManager
{
    protected array $steps = [];

    public function steps(array $steps): self
    {
        $this->steps = $steps;

        return $this;
    }

    public function run(mixed $payload): mixed
    {
        return app(LaravelPipeline::class)
            ->send($payload)
            ->through($this->steps)
            ->then(function ($payload): mixed {
                return $payload;
            });
    }

    public function queued(mixed $payload): void
    {
        foreach ($this->steps as $step) {
            $step::dispatch($payload);
        }
    }
}
