<?php

declare(strict_types=1);

namespace Synetro\Fuse\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Synetro\Fuse\Config\ConfigManager;

class InstallCommand extends Command
{
    protected $signature = 'fuse:install
                            {--force : Force re-installation}
                            {--no-migrations : Skip publishing migrations}
                            {--no-config : Skip publishing configuration}
                            {--no-routes : Skip publishing routes}
                            {--no-middleware : Skip publishing middleware}';

    protected $description = 'Install the Fuse package';

    public function handle(ConfigManager $config): int
    {
        $this->info('Fuse Installation');
        $this->line('Powerful Laravel applications. Without the boilerplate.');
        $this->newLine();

        if (! $this->option('no-config')) {
            $this->publishConfig($config);
        }

        if (! $this->option('no-migrations')) {
            $this->publishMigrations();
        }

        if (! $this->option('no-routes')) {
            $this->publishRoutes();
        }

        if (! $this->option('no-middleware')) {
            $this->publishMiddleware();
        }

        $this->info('');
        $this->info('Fuse installed successfully!');
        $this->info('Run `php artisan fuse:doctor` to verify your setup.');
        $this->info('Run `php artisan fuse:make Product` to generate your first resource.');

        return Command::SUCCESS;
    }

    protected function publishConfig(ConfigManager $config): void
    {
        $published = $config->publish();

        if ($published) {
            $this->info('✓ Configuration published to config/fuse.php');
        } else {
            $this->warn('⚠ Configuration already exists (use --force to overwrite)');
        }
    }

    protected function publishMigrations(): void
    {
        $migrations = [
            '2026_01_01_000001_create_fuse_configs_table.php',
            '2026_01_01_000002_create_fuse_secrets_table.php',
            '2026_01_01_000003_create_fuse_features_table.php',
            '2026_01_01_000004_create_fuse_audits_table.php',
            '2026_01_01_000005_create_fuse_webhooks_table.php',
            '2026_01_01_000006_create_fuse_files_table.php',
        ];

        $published = 0;

        foreach ($migrations as $migration) {
            $source = __DIR__.'/../../database/migrations/'.$migration;
            $target = database_path('migrations/'.$migration);

            if (File::exists($target) && ! $this->option('force')) {
                continue;
            }

            if (! File::exists($source)) {
                continue;
            }

            $stub = file_get_contents($source);

            File::put($target, $stub);
            $published++;
        }

        if ($published > 0) {
            $this->info("✓ {$published} migration(s) published");
        } else {
            $this->warn('⚠ Migrations already published (use --force to overwrite)');
        }
    }

    protected function publishRoutes(): void
    {
        $this->info('✓ Routes registered via FuseServiceProvider');
    }

    protected function publishMiddleware(): void
    {
        $this->info('✓ Middleware registered via FuseServiceProvider');
    }
}
