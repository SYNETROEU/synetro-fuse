<?php

declare(strict_types=1);

namespace Synetro\Fuse\Resources;

use Illuminate\Routing\Router;
use Illuminate\Support\Str;

class ResourceRouteRegistrar
{
    protected array $registered = [];

    public function __construct(protected Router $router) {}

    public function register(ResourceDefinition $resource): void
    {
        $uri = $resource->uri();
        $parameter = $resource->parameter();
        $controller = $resource->controller() ?? FuseResourceController::class;
        $middleware = $resource->middleware();

        if (isset($this->registered[$uri])) {
            return;
        }

        $this->registered[$uri] = true;

        $this->router->middleware($middleware)
            ->prefix($uri)
            ->as($uri . '.')
            ->group(function ($router) use ($controller, $parameter) {
                $router->get('/', [$controller, 'index'])
                    ->name('index');

                $router->post('/', [$controller, 'store'])
                    ->name('store');

                $router->get('/{' . $parameter . '}', [$controller, 'show'])
                    ->name('show');

                $router->put('/{' . $parameter . '}', [$controller, 'update'])
                    ->name('update');

                $router->patch('/{' . $parameter . '}', [$controller, 'update'])
                    ->name('update');

                $router->delete('/{' . $parameter . '}', [$controller, 'destroy'])
                    ->name('destroy');
            });
    }
}
