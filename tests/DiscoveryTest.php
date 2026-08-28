<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Synetro\Fuse\Support\Facades\Fuse;

class DiscoveryTest extends TestCase
{
    public function test_discover_returns_classes(): void
    {
        $discovery = Fuse::auto();

        $this->assertIsArray($results = $discovery->auto());
    }

    public function test_discovery_can_be_disabled(): void
    {
        $discovery = Fuse::auto();
        $discovery->disable();

        $results = $discovery->auto();

        $this->assertIsArray($results);
    }
}
