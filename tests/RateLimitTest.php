<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Synetro\Fuse\Tests\TestCase;

class RateLimitTest extends TestCase
{
    public function test_rate_limit_allows_within_limit(): void
    {
        $limiter = \Synetro\Fuse\Support\Facades\Fuse::limit('test');

        $this->assertTrue($limiter->check('test-key', 5, 60));
    }

    public function test_rate_limit_blocks_after_limit(): void
    {
        $limiter = \Synetro\Fuse\Support\Facades\Fuse::limit('test');

        for ($i = 0; $i < 5; $i++) {
            $limiter->check('rate-limit-key', 3, 60);
        }

        $this->assertFalse($limiter->check('rate-limit-key', 3, 60));
    }
}
