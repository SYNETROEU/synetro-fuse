<?php

declare(strict_types=1);

namespace Synetro\Fuse\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class RoutesCommand extends Command
{
    protected $signature = 'fuse:routes';

    protected $description = 'List Fuse registered routes';

    public function handle(): int
    {
        $this->info('Fuse Routes');
        $this->line('===========');

        $fuseRoutes = Route::getRoutes()->getRoutes();

        $rows = [];
        foreach ($fuseRoutes as $route) {
            if (str_contains($route->getPrefix(), 'fuse')) {
                $rows[] = [
                    $route->getPrefix().$route->uri(),
                    implode('|', $route->methods()),
                    $route->getActionName(),
                ];
            }
        }

        $this->table(['URI', 'Method', 'Action'], $rows);

        return Command::SUCCESS;
    }
}
