<?php

declare(strict_types=1);

namespace Synetro\Fuse\Support;

use Illuminate\Contracts\Container\Container;
use Synetro\Fuse\Config\ConfigManager;

class FuseTenant extends Fuse
{
    public function __construct(
        Container $app,
        protected mixed $tenant,
    ) {
        parent::__construct($app);
    }

    public function config(string $key): mixed
    {
        return app(ConfigManager::class)->forTenant($this->tenant)->get($key);
    }
}
