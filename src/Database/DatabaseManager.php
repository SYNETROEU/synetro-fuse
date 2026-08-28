<?php

declare(strict_types=1);

namespace Synetro\Fuse\Database;

use Illuminate\Database\Connection;
use Illuminate\Support\Collection;

class DatabaseManager
{
    public function __construct(
        protected Connection $db,
    ) {}

    public function health(): DatabaseHealth
    {
        try {
            $this->db->getPdo();
            $this->db->getDatabaseName();
        } catch (\Throwable $e) {
            return DatabaseHealth::fail($e->getMessage());
        }

        return DatabaseHealth::pass();
    }

    public function size(): string
    {
        $result = $this->db->selectOne('SELECT SUM(data_length + index_length) as size FROM information_schema.tables WHERE table_schema = ?', [$this->db->getDatabaseName()]);

        return number_format($result->size / 1024 / 1024, 2) . ' MB';
    }

    public function tables(): Collection
    {
        $tables = $this->db->select('SHOW TABLES');

        return collect($tables)->map(fn ($table) => array_values((array) $table)[0]);
    }

    public function slowQueries(): array
    {
        return [];
    }
}
