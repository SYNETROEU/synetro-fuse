<?php

declare(strict_types=1);

namespace Synetro\Fuse\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ModelsCommand extends Command
{
    protected $signature = 'fuse:models';

    protected $description = 'List application models';

    public function handle(): int
    {
        $this->info('Application Models');
        $this->line('==================');

        $modelPath = app_path('Models');

        if (! File::exists($modelPath)) {
            $this->warn('No Models directory found.');

            return Command::SUCCESS;
        }

        $files = File::files($modelPath);

        foreach ($files as $file) {
            $className = $file->getBasename('.php');
            $this->line('  - '.$className);
        }

        return Command::SUCCESS;
    }
}
