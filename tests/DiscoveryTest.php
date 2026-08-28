<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Synetro\Fuse\Tests\TestCase;

class DiscoveryTest extends TestCase
{
    public function test_discover_returns_classes(): void
    {
        $discovery = \Synetro\Fuse\Support\Facades\Fuse::auto();

        $this->assertIsArray($results = $discovery->auto());
    }

    public function test_discovery_can_be_disabled(): void
    {
        $discovery = \Synetro\Fuse\Support\Facades\Fuse::auto();
        $discovery->disable();

        $results = $discovery->auto();

        $this->assertIsArray($results);
    }
}
