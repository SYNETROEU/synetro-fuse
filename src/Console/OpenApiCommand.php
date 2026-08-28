<?php

declare(strict_types=1);

namespace Synetro\Fuse\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class OpenApiCommand extends Command
{
    protected $signature = 'fuse:openapi
                            {--output=openapi.json : Output file path}
                            {--yaml : Output as YAML}';

    protected $description = 'Generate OpenAPI documentation';

    public function handle(): int
    {
        $routes = Route::getRoutes()->getRoutes();

        $spec = [
            'openapi' => '3.0.0',
            'info' => [
                'title' => config('app.name', 'Laravel') . ' API',
                'version' => '1.0.0',
            ],
            'paths' => [],
        ];

        foreach ($routes as $route) {
            $uri = $route->uri();
            $methods = $route->methods();

            foreach ($methods as $method) {
                if (in_array(strtolower($method), ['get', 'post', 'put', 'patch', 'delete'])) {
                    $spec['paths']["/{$uri}"][strtolower($method)] = [
                        'summary' => $route->getActionName(),
                        'responses' => [
                            '200' => ['description' => 'Success'],
                        ],
                    ];
                }
            }
        }

        $output = $this->option('yaml') ? $this->toYaml($spec) : json_encode($spec, JSON_PRETTY_PRINT);

        $filePath = $this->option('output');
        file_put_contents($filePath, $output);

        $this->info("OpenAPI spec generated: {$filePath}");

        return Command::SUCCESS;
    }

    protected function toYaml(array $spec): string
    {
        return "openapi: 3.0.0\ninfo:\n  title: {$spec['info']['title']}\n  version: {$spec['info']['version']}\n";
    }
}
