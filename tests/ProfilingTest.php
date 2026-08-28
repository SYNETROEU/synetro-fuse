<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Synetro\Fuse\Tests\TestCase;

class ProfilingTest extends TestCase
{
    public function test_profile_returns_metrics_in_debug_mode(): void
    {
        config()->set('app.debug', true);

        $result = \Synetro\Fuse\Support\Facades\Fuse::profile(function () {
            return 'result';
        });

        $this->assertArrayHasKey('queries', $result);
        $this->assertArrayHasKey('query_count', $result);
        $this->assertArrayHasKey('time', $result);
        $this->assertArrayHasKey('memory', $result);
    }

    public function test_profile_returns_empty_outside_debug(): void
    {
        config()->set('app.debug', false);

        $result = \Synetro\Fuse\Support\Facades\Fuse::profile(function () {
            return 'result';
        });

        $this->assertSame([], $result);
    }
}
