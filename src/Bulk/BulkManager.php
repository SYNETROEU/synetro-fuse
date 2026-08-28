<?php

declare(strict_types=1);

namespace Synetro\Fuse\Bulk;

class BulkManager
{
    public function __construct(protected \Illuminate\Database\Query\Builder $query) {}

    public function update(array $values): int
    {
        return $this->query->update($values);
    }

    public function delete(): int
    {
        return $this->query->delete();
    }

    public function restore(): int
    {
        if (method_exists($this->query, 'restore')) {
            return $this->query->restore();
        }

        return 0;
    }

    public function chunk(int $chunkSize, callable $callback): void
    {
        $this->query->chunk($chunkSize, function ($records) use ($callback) {
            foreach ($records as $record) {
                $callback($record);
            }
        });
    }
}
