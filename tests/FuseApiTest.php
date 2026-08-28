<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Synetro\Fuse\Tests\TestCase;

class FuseApiTest extends TestCase
{
    public function test_config_can_be_retrieved(): void
    {
        $this->assertNotNull(\Synetro\Fuse\Support\Facades\Fuse::config('name'));
    }

    public function test_feature_manager_can_be_resolved(): void
    {
        $this->assertInstanceOf(\Synetro\Fuse\Features\FeatureManager::class, \Synetro\Fuse\Support\Facades\Fuse::feature('test'));
    }

    public function test_cache_manager_can_be_resolved(): void
    {
        $this->assertInstanceOf(\Synetro\Fuse\Cache\FuseCacheManager::class, \Synetro\Fuse\Support\Facades\Fuse::cache('test'));
    }

    public function test_health_manager_can_be_resolved(): void
    {
        $this->assertInstanceOf(\Synetro\Fuse\Health\HealthManager::class, \Synetro\Fuse\Support\Facades\Fuse::health());
    }

    public function test_security_manager_can_be_resolved(): void
    {
        $this->assertInstanceOf(\Synetro\Fuse\Security\SecurityManager::class, \Synetro\Fuse\Support\Facades\Fuse::security());
    }

    public function test_log_manager_can_be_resolved(): void
    {
        $this->assertInstanceOf(\Synetro\Fuse\Logging\LogManager::class, \Synetro\Fuse\Support\Facades\Fuse::log());
    }

    public function test_metrics_manager_can_be_resolved(): void
    {
        $this->assertInstanceOf(\Synetro\Fuse\Metrics\MetricsManager::class, \Synetro\Fuse\Support\Facades\Fuse::metrics());
    }

    public function test_notification_manager_can_be_resolved(): void
    {
        $this->assertInstanceOf(\Synetro\Fuse\Notifications\NotificationManager::class, \Synetro\Fuse\Support\Facades\Fuse::notify());
    }

    public function test_api_manager_can_be_resolved(): void
    {
        $this->assertInstanceOf(\Synetro\Fuse\Api\ApiManager::class, \Synetro\Fuse\Support\Facades\Fuse::api());
    }

    public function test_auth_manager_can_be_resolved(): void
    {
        $this->assertInstanceOf(\Synetro\Fuse\Auth\AuthManager::class, \Synetro\Fuse\Support\Facades\Fuse::auth());
    }
}
