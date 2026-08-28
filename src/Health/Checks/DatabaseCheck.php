<?php

declare(strict_types=1);

namespace Synetro\Fuse\Health\Checks;

use Illuminate\Support\Facades\DB;
use Synetro\Fuse\Health\HealthCheckInterface;
use Synetro\Fuse\Health\HealthResult;

class DatabaseCheck implements HealthCheckInterface
{
    public function check(): HealthResult
    {
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            return HealthResult::fail('database', $e->getMessage());
        }

        return HealthResult::pass('database', 'Connected');
    }
}
