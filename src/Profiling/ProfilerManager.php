<?php

declare(strict_types=1);

namespace Synetro\Fuse\Profiling;

use Illuminate\Support\Facades\DB;

class ProfilerManager
{
    protected ?array $queries = null;

    protected ?float $startTime = null;

    protected ?float $endTime = null;

    protected ?int $memoryStart = null;

    public function start(): void
    {
        if (! config('app.debug')) {
            return;
        }

        $this->startTime = microtime(true);
        $this->memoryStart = memory_get_usage(true);
        $this->queries = [];

        DB::listen(function ($query) {
            if ($this->queries !== null) {
                $this->queries[] = [
                    'sql' => $query->sql,
                    'time' => $query->time,
                ];
            }
        });
    }

    public function stop(): array
    {
        if (! config('app.debug')) {
            return [];
        }

        $this->endTime = microtime(true);

        return [
            'queries' => $this->queries ?? [],
            'query_count' => count($this->queries ?? []),
            'time' => ($this->endTime - ($this->startTime ?? $this->endTime)) * 1000,
            'memory' => $this->memoryStart ? (memory_get_usage(true) - $this->memoryStart) : 0,
        ];
    }
}
