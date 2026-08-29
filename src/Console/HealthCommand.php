<?php

declare(strict_types=1);

namespace Synetro\Fuse\Console;

use Illuminate\Console\Command;
use Synetro\Fuse\Health\HealthManager;

class HealthCommand extends Command
{
    protected $signature = 'fuse:health';

    protected $description = 'Run Fuse health checks';

    public function handle(HealthManager $health): int
    {
        $results = $health->all();

        $rows = collect($results)->map(fn ($result, $name) => [
            $name,
            $result->passed() ? 'OK' : ($result->degraded() ? 'WARN' : 'FAIL'),
            $result->message,
        ])->toArray();

        $this->table(['Check', 'Status', 'Message'], $rows);

        $status = $health->status();

        if ($status === 'healthy') {
            $this->info('Overall: HEALTHY');

            return Command::SUCCESS;
        }

        if ($status === 'degraded') {
            $this->warn('Overall: DEGRADED');

            return Command::SUCCESS;
        }

        $this->error('Overall: FAILED');

        return Command::FAILURE;
    }
}
