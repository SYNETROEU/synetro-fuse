<?php

declare(strict_types=1);

namespace Synetro\Fuse\Console;

use Illuminate\Console\Command;

class DocsCommand extends Command
{
    protected $signature = 'fuse:docs';

    protected $description = 'Generate application API documentation';

    public function handle(): int
    {
        $this->info('Generating documentation...');

        $this->callSilent('fuse:openapi', [
            '--output' => base_path('docs/openapi.json'),
        ]);

        $this->info('✓ Documentation generated: docs/openapi.json');

        return Command::SUCCESS;
    }
}
