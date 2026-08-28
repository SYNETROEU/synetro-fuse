<?php

declare(strict_types=1);

namespace Synetro\Fuse\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class JobsCommand extends Command
{
    protected $signature = 'fuse:jobs';
    protected $description = 'List application jobs';

    public function handle(): int
    {
        $this->info('Application Jobs');
        $this->line('=================');

        $dirs = [
            app_path('Jobs'),
            app_path('Listeners'),
        ];

        foreach ($dirs as $dir) {
            if (!File::exists($dir)) {
                continue;
            }

            $files = File::allFiles($dir);
            foreach ($files as $file) {
                $className = $file->getBasename('.php');
                $this->line('  - ' . $className);
            }
        }

        return Command::SUCCESS;
    }
}
