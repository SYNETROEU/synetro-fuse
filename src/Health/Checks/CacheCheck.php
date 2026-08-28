<?php

declare(strict_types=1);

namespace Synetro\Fuse\Health\Checks;

use Illuminate\Support\Facades\Cache;
use Synetro\Fuse\Health\HealthCheckInterface;
use Synetro\Fuse\Health\HealthResult;

class CacheCheck implements HealthCheckInterface
{
    public function check(): HealthResult
    {
        try {
            Cache::put('fuse-health-check', true, 10);
            Cache::get('fuse-health-check');
            Cache::forget('fuse-health-check');
        } catch (\Throwable $e) {
            return HealthResult::fail('cache', $e->getMessage());
        }

        return HealthResult::pass('cache', 'Operational');
    }
}
