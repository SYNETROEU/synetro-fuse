<?php

declare(strict_types=1);

namespace Synetro\Fuse\Console;

use Illuminate\Console\Command;

class OptimizeCommand extends Command
{
    protected $signature = 'fuse:optimize
                            {--clear : Clear all caches}';

    protected $description = 'Optimize Fuse and Laravel caches';

    public function handle(): int
    {
        if ($this->option('clear')) {
            $this->callSilent('config:clear');
            $this->callSilent('route:clear');
            $this->callSilent('view:clear');
            $this->callSilent('event:clear');
            $this->info('✓ All caches cleared');

            return Command::SUCCESS;
        }

        $this->info('Optimizing Fuse...');

        $this->callSilent('config:cache');
        $this->callSilent('route:cache');
        $this->callSilent('event:cache');
        $this->callSilent('view:cache');

        $this->info('✓ Fuse optimized');
        $this->info('  config: cached');
        $this->info('  routes: cached');
        $this->info('  events: cached');
        $this->info('  views: cached');

        return Command::SUCCESS;
    }
}
