<?php

declare(strict_types=1);

namespace Synetro\Fuse\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class EventsCommand extends Command
{
    protected $signature = 'fuse:events';

    protected $description = 'List application events';

    public function handle(): int
    {
        $this->info('Application Events');
        $this->line('===================');

        $dirs = [
            app_path('Events'),
            app_path('Listeners'),
        ];

        foreach ($dirs as $dir) {
            if (! File::exists($dir)) {
                continue;
            }

            $files = File::allFiles($dir);
            foreach ($files as $file) {
                $className = $file->getBasename('.php');
                $this->line('  - '.$className);
            }
        }

        return Command::SUCCESS;
    }
}
