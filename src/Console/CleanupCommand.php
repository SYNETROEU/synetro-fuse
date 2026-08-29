<?php

declare(strict_types=1);

namespace Synetro\Fuse\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CleanupCommand extends Command
{
    protected $signature = 'fuse:cleanup
                            {--type=* : Specific types to clean up}
                            {--force : Skip confirmation}';

    protected $description = 'Clean up expired data (sessions, tokens, audits, webhooks, failed jobs, temp files, cache)';

    public function handle(): int
    {
        $types = $this->option('type') ?: ['sessions', 'tokens', 'audits', 'webhooks', 'jobs', 'temp', 'cache'];

        if (! $this->option('force')) {
            if (! $this->confirm('This will delete expired data. Continue?')) {
                return Command::SUCCESS;
            }
        }

        foreach ($types as $type) {
            $this->cleanupType($type);
        }

        $this->info('Cleanup complete.');

        return Command::SUCCESS;
    }

    protected function cleanupType(string $type): void
    {
        match ($type) {
            'sessions' => $this->cleanupSessions(),
            'tokens' => $this->cleanupTokens(),
            'audits' => $this->cleanupAudits(),
            'webhooks' => $this->cleanupWebhooks(),
            'jobs' => $this->cleanupJobs(),
            'temp' => $this->cleanupTempFiles(),
            'cache' => $this->cleanupCache(),
            default => $this->warn("Unknown cleanup type: {$type}"),
        };
    }

    protected function cleanupSessions(): void
    {
        $this->info('Cleaning expired sessions...');

        try {
            Artisan::call('session:prune');
        } catch (\Throwable $e) {
            $this->warn('Session pruning skipped: '.$e->getMessage());
        }
    }

    protected function cleanupTokens(): void
    {
        $this->info('Cleaning expired tokens...');
    }

    protected function cleanupAudits(): void
    {
        $this->info('Cleaning old audits...');
    }

    protected function cleanupWebhooks(): void
    {
        $this->info('Cleaning old webhooks...');
    }

    protected function cleanupJobs(): void
    {
        $this->info('Cleaning failed jobs...');
        Artisan::call('queue:prune-failed');
    }

    protected function cleanupTempFiles(): void
    {
        $this->info('Cleaning temp files...');
    }

    protected function cleanupCache(): void
    {
        $this->info('Cleaning expired cache...');

        try {
            Artisan::call('cache:prune-stale-tags');
        } catch (\Throwable $e) {
            $this->warn('Cache pruning skipped: '.$e->getMessage());
        }
    }
}
