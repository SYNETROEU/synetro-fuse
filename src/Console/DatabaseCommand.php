<?php

declare(strict_types=1);

namespace Synetro\Fuse\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DatabaseCommand extends Command
{
    protected $signature = 'fuse:database
                            {connection? : Database connection}
                            {--size : Show database size}
                            {--tables : List tables}
                            {--slow : Show slow queries}';

    protected $description = 'Database diagnostics';

    public function handle(): int
    {
        $connection = $this->argument('connection') ?: config('database.default');

        if ($this->option('size')) {
            $this->showDatabaseSize($connection);
        }

        if ($this->option('tables')) {
            $this->showTables($connection);
        }

        if ($this->option('slow')) {
            $this->showSlowQueries($connection);
        }

        return Command::SUCCESS;
    }

    protected function showDatabaseSize(string $connection): void
    {
        $size = DB::connection($connection)
            ->selectOne('SELECT SUM(data_length + index_length) as size FROM information_schema.tables WHERE table_schema = DATABASE()');

        $this->info('Database Size: '.number_format($size->size / 1024 / 1024, 2).' MB');
    }

    protected function showTables(string $connection): void
    {
        $driver = DB::connection($connection)->getDriverName();

        if ($driver === 'sqlite') {
            $tables = DB::connection($connection)
                ->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            $this->info('Tables:');
            foreach ($tables as $table) {
                $this->line('  - '.$table->name);
            }
        } else {
            $tables = DB::connection($connection)->select('SHOW TABLES');
            $this->info('Tables:');
            foreach ($tables as $table) {
                $this->line('  - '.array_values((array) $table)[0]);
            }
        }
    }

    protected function showSlowQueries(string $connection): void
    {
        $this->info('Slow Queries:');
        $this->line('  (Requires slow query log to be enabled)');
    }
}
