<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Synetro\Fuse\Tests\TestCase;

class LocksTest extends TestCase
{
    public function test_lock_runs_callback(): void
    {
        $result = \Synetro\Fuse\Support\Facades\Fuse::lock('test-lock')->run(function () {
            return 'locked-result';
        });

        $this->assertSame('locked-result', $result);
    }

    public function test_lock_prevents_concurrent_execution(): void
    {
        $results = [];

        \Synetro\Fuse\Support\Facades\Fuse::lock('concurrent-lock')->run(function () use (&$results) {
            $results[] = 'first';
            return 'first';
        });

        $this->assertCount(1, $results);
    }
}
