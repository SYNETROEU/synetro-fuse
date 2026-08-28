<?php

declare(strict_types=1);

namespace Synetro\Fuse\Health;

interface HealthCheckInterface
{
    public function check(): HealthResult;
}
