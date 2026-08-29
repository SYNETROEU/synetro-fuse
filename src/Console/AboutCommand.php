<?php

declare(strict_types=1);

namespace Synetro\Fuse\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Synetro\Fuse\Resources\ResourceManager;

class AboutCommand extends Command
{
    protected $signature = 'fuse:about';

    protected $description = 'Display Fuse application information';

    public function handle(ResourceManager $resources): int
    {
        $this->info('Fuse — Application Overview');
        $this->line('==============================');
        $this->newLine();

        $this->info('Environment: '.app()->environment());
        $this->info('Laravel: '.app()->version());
        $this->info('PHP: '.PHP_VERSION);
        $this->newLine();

        $this->info('Routes:');
        $routes = Route::getRoutes();
        $this->line('  Total: '.$routes->count());

        $resourcesList = $resources->all();
        if (! empty($resourcesList)) {
            $this->info('');
            $this->info('Registered Resources:');
            foreach ($resourcesList as $resource) {
                $this->line('  - '.($resource->uri() ?? 'unknown'));
            }
        }

        return Command::SUCCESS;
    }
}
