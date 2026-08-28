<?php

declare(strict_types=1);

namespace Synetro\Fuse\Console;

use Illuminate\Console\Command;
use Synetro\Fuse\Config\ConfigManager;
use Synetro\Fuse\Database\DatabaseManager;
use Synetro\Fuse\Health\HealthManager;
use Synetro\Fuse\Support\Facades\Fuse;

class DoctorCommand extends Command
{
    protected $signature = 'fuse:doctor
                            {--json : Output as JSON}
                            {--check=* : Specific checks to run}';

    protected $description = 'Diagnose Fuse and Laravel health';

    public function handle(HealthManager $health, DatabaseManager $database, ConfigManager $config): int
    {
        $checks = $this->runDiagnostics($health, $database, $config);

        if ($this->option('json')) {
            $this->outputJson($checks);
        } else {
            $this->outputTable($checks);
        }

        $failed = collect($checks)->filter(fn ($c) => $c['status'] === 'fail')->count();

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    protected function runDiagnostics(HealthManager $health, DatabaseManager $database, ConfigManager $config): array
    {
        $checks = [];

        $checks[] = [
            'name' => 'PHP',
            'status' => version_compare(PHP_VERSION, '8.2.0', '>=') ? 'pass' : 'fail',
            'message' => 'PHP ' . PHP_VERSION,
        ];

        $laravelVersion = app()->version();
        $checks[] = [
            'name' => 'Laravel',
            'status' => version_compare($laravelVersion, '11.0.0', '>=') ? 'pass' : 'fail',
            'message' => 'Laravel ' . $laravelVersion,
        ];

        try {
            $database->health();
            $checks[] = ['name' => 'Database', 'status' => 'pass', 'message' => 'Connected'];
        } catch (\Throwable $e) {
            $checks[] = ['name' => 'Database', 'status' => 'fail', 'message' => $e->getMessage()];
        }

        try {
            $health->check('cache');
            $checks[] = ['name' => 'Cache', 'status' => 'pass', 'message' => 'Operational'];
        } catch (\Throwable $e) {
            $checks[] = ['name' => 'Cache', 'status' => 'warn', 'message' => $e->getMessage()];
        }

        try {
            $health->check('queue');
            $checks[] = ['name' => 'Queue', 'status' => 'pass', 'message' => 'Operational'];
        } catch (\Throwable $e) {
            $checks[] = ['name' => 'Queue', 'status' => 'warn', 'message' => $e->getMessage()];
        }

        try {
            $health->check('storage');
            $checks[] = ['name' => 'Storage', 'status' => 'pass', 'message' => 'Writable'];
        } catch (\Throwable $e) {
            $checks[] = ['name' => 'Storage', 'status' => 'fail', 'message' => $e->getMessage()];
        }

        if (config('app.debug')) {
            $checks[] = ['name' => 'Debug', 'status' => 'warn', 'message' => 'APP_DEBUG=true'];
        }

        return $checks;
    }

    protected function outputTable(array $checks): void
    {
        $rows = collect($checks)->map(fn ($c) => [
            $c['name'],
            match ($c['status']) {
                'pass' => $this->style('✓', 'green'),
                'warn' => $this->style('⚠', 'yellow'),
                default  => $this->style('✗', 'red'),
            },
            $c['message'],
        ])->toArray();

        $this->table(['Check', 'Status', 'Message'], $rows);

        $failed = collect($checks)->filter(fn ($c) => $c['status'] === 'fail')->count();
        $warned = collect($checks)->filter(fn ($c) => $c['status'] === 'warn')->count();

        if ($failed > 0) {
            $this->error("Overall: FAILED ({$failed} failed)");
        } elseif ($warned > 0) {
            $this->warn("Overall: DEGRADED ({$warned} warnings)");
        } else {
            $this->info('Overall: HEALTHY');
        }
    }

    protected function outputJson(array $checks): void
    {
        $this->line(json_encode($checks, JSON_PRETTY_PRINT));
    }
}
