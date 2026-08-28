<?php

declare(strict_types=1);

namespace Synetro\Fuse\Health\Checks;

use Illuminate\Filesystem\Filesystem;
use Synetro\Fuse\Health\HealthCheckInterface;
use Synetro\Fuse\Health\HealthResult;

class StorageCheck implements HealthCheckInterface
{
    public function __construct(
        protected Filesystem $files,
    ) {}

    public function check(): HealthResult
    {
        $storagePath = storage_path('app');

        if (!is_writable($storagePath)) {
            return HealthResult::fail('storage', 'Storage not writable');
        }

        return HealthResult::pass('storage', 'Writable');
    }
}
