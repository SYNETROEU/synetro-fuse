<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Synetro\Fuse\Health\Checks\DatabaseCheck;
use Synetro\Fuse\Health\HealthManager;
use Synetro\Fuse\Health\HealthResult;

class HealthManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'sqlite'])->run();
    }

    public function test_database_check_passes(): void
    {
        $check = new DatabaseCheck;
        $result = $check->check();

        $this->assertInstanceOf(HealthResult::class, $result);
        $this->assertTrue($result->passed());
    }

    public function test_health_manager_returns_status(): void
    {
        $manager = app(HealthManager::class);
        $status = $manager->status();

        $this->assertIsString($status);
    }
}
