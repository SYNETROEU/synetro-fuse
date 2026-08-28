<?php

declare(strict_types=1);

namespace Synetro\Fuse\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Synetro\Fuse\Resources\ResourceManager;

class GenerateCommand extends Command
{
    protected $signature = 'fuse:make
                            {name : The resource name}
                            {--full : Generate full CRUD}
                            {--model : Generate model}
                            {--controller : Generate controller}
                            {--migration : Generate migration}
                            {--factory : Generate factory}
                            {--seed : Generate seeder}
                            {--resource : Generate API resource}
                            {--policy : Generate policy}
                            {--action : Generate action}
                            {--test : Generate test}
                            {--module : Generate feature module}
                            {--force : Force overwrite}';

    protected $description = 'Generate Fuse resources and components';

    protected array $components = [];

    public function handle(ResourceManager $resources): int
    {
        $name = Str::studly($this->argument('name'));

        if ($this->option('full')) {
            $this->components = ['model', 'migration', 'factory', 'controller', 'resource', 'policy', 'action', 'test'];
        } else {
            $this->components = array_filter([
                'model' => $this->option('model'),
                'migration' => $this->option('migration'),
                'factory' => $this->option('factory'),
                'seed' => $this->option('seed'),
                'controller' => $this->option('controller'),
                'resource' => $this->option('resource'),
                'policy' => $this->option('policy'),
                'action' => $this->option('action'),
                'test' => $this->option('test'),
            ]);
        }

        if (empty($this->components)) {
            $this->error('Please specify at least one component to generate.');
            return Command::FAILURE;
        }

        $this->info("Generating {$name}...");

        foreach ($this->components as $component) {
            $this->generateComponent($name, $component);
        }

        $this->newLine();
        $this->info("Generated {$name} successfully!");

        return Command::SUCCESS;
    }

    protected function generateComponent(string $name, string $component): void
    {
        $stub = $this->resolveStub($component);
        $path = $this->resolvePath($name, $component);

        if (file_exists($path) && !$this->option('force')) {
            $this->warn("  ~ {$component} already exists: {$path}");
            return;
        }

        $content = str_replace('{{class}}', $name, file_get_contents($stub));

        file_put_contents($path, $content);
        $this->info("  ✓ {$component}: {$path}");
    }

    protected function resolveStub(string $component): string
    {
        $stubPath = __DIR__ . '/../../stubs/' . $component . '.stub';

        if (file_exists($stubPath)) {
            return $stubPath;
        }

        return __DIR__ . '/../../stubs/default.stub';
    }

    protected function resolvePath(string $name, string $component): string
    {
        $paths = [
            'model' => app_path('Models/' . $name . '.php'),
            'migration' => database_path('migrations/' . date('Y_m_d_His') . '_create_' . Str::snake(Str::plural($name)) . '_table.php'),
            'factory' => database_path('factories/' . $name . 'Factory.php'),
            'seed' => database_path('seeders/' . $name . 'Seeder.php'),
            'controller' => app_path('Http/Controllers/' . $name . 'Controller.php'),
            'resource' => app_path('Http/Resources/' . $name . 'Resource.php'),
            'policy' => app_path('Policies/' . $name . 'Policy.php'),
            'action' => app_path('Actions/' . $name . 'Action.php'),
            'test' => tests_path('Feature/' . $name . 'Test.php'),
        ];

        return $paths[$component] ?? app_path($name . '.php');
    }
}
