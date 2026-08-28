<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Synetro\Fuse\Tests\TestCase;

class IdempotencyTest extends TestCase
{
    public function test_idempotent_runs_callback_once(): void
    {
        $calls = 0;

        $result = \Synetro\Fuse\Support\Facades\Fuse::idempotent('test-key')->run(function () use (&$calls) {
            $calls++;

            return 'result';
        });

        $this->assertSame('result', $result);
        $this->assertSame(1, $calls);

        $cached = \Synetro\Fuse\Support\Facades\Fuse::idempotent('test-key')->run(function () use (&$calls) {
            $calls++;

            return 'cached-result';
        });

        $this->assertSame('result', $cached);
        $this->assertSame(1, $calls);
    }
}
