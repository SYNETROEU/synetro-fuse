<?php

declare(strict_types=1);

namespace Synetro\Fuse\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Routing\Router;
use Illuminate\Auth\Access\AuthorizationException;
use Synetro\Fuse\Query\FuseQuery;
use Synetro\Fuse\Exceptions\ResourceException;

class ResourceManager
{
    protected array $registered = [];

    public function __construct(protected Router $router) {}

    public function for(string $model): ResourceBuilder
    {
        return new ResourceBuilder($model, $this);
    }

    public function register(string $name, array $config): void
    {
        $this->registered[$name] = $config;

        if (config('fuse.routes.auto_register', true)) {
            $this->registerRoutes($name, $config);
        }
    }

    public function all(): array
    {
        return array_keys($this->registered);
    }

    protected function registerRoutes(string $name, array $config): void
    {
        $uri = strtolower($name) . 's';
        $controller = $config['controller'] ?? "{$name}Controller";

        $this->router->middleware(config('fuse.routes.middleware', ['web', 'api']))
            ->prefix(config('fuse.routes.prefix', '') . '/' . $uri)
            ->group(function ($router) use ($name, $controller, $config) {
                $router->get('/', [$controller, 'index']);
                $router->post('/', [$controller, 'store']);
                $router->get('/{id}', [$controller, 'show']);
                $router->put('/{id}', [$controller, 'update']);
                $router->patch('/{id}', [$controller, 'update']);
                $router->delete('/{id}', [$controller, 'destroy']);
            });
    }
}
