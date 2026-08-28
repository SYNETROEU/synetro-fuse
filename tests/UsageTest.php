<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Synetro\Fuse\Support\Facades\Fuse;

class UsageTest extends TestCase
{
    public function test_usage_consume_increments(): void
    {
        $manager = Fuse::usage('user-1', 'feature')->limit(10);

        $this->assertTrue($manager->consume(1));
        $this->assertSame(1, $manager->usage());
    }

    public function test_usage_blocks_after_limit(): void
    {
        $manager = Fuse::usage('user-1', 'feature')->limit(5);

        $this->assertTrue($manager->consume(5));
        $this->assertFalse($manager->consume(1));
    }
}
