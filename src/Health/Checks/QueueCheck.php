<?php

declare(strict_types=1);

namespace Synetro\Fuse\Health\Checks;

use Illuminate\Support\Facades\Queue;
use Synetro\Fuse\Health\HealthCheckInterface;
use Synetro\Fuse\Health\HealthResult;

class QueueCheck implements HealthCheckInterface
{
    public function check(): HealthResult
    {
        try {
            $connection = config('queue.default');
            $queue = Queue::connection($connection);

            if (!$queue->connected()) {
                return HealthResult::warn('queue', 'Connection not verified');
            }
        } catch (\Throwable $e) {
            return HealthResult::warn('queue', $e->getMessage());
        }

        return HealthResult::pass('queue', 'Operational');
    }
}
