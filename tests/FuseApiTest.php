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
        $this->assertInstanceOf(\Synetro\Fuse\Features\Feature::class, \Synetro\Fuse\Support\Facades\Fuse::feature('test'));
    }

    public function test_cache_manager_can_be_resolved(): void
    {
        $result = \Synetro\Fuse\Support\Facades\Fuse::cacheFor('test', 60, fn () => 'cached');
        $this->assertSame('cached', $result);
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

    public function test_validation_manager_can_be_resolved(): void
    {
        $validator = \Synetro\Fuse\Support\Facades\Fuse::validate([], []);
        $this->assertInstanceOf(\Synetro\Fuse\Validation\Validator::class, $validator);
    }

    public function test_idempotency_manager_can_be_resolved(): void
    {
        $manager = \Synetro\Fuse\Support\Facades\Fuse::idempotent('key');
        $this->assertInstanceOf(\Synetro\Fuse\Idempotency\IdempotencyManager::class, $manager);
    }

    public function test_lock_manager_can_be_resolved(): void
    {
        $manager = \Synetro\Fuse\Support\Facades\Fuse::lock('name');
        $this->assertInstanceOf(\Synetro\Fuse\Locks\LockManager::class, $manager);
    }

    public function test_rate_limiter_can_be_resolved(): void
    {
        $limiter = \Synetro\Fuse\Support\Facades\Fuse::limit('name');
        $this->assertInstanceOf(\Synetro\Fuse\RateLimit\RateLimiter::class, $limiter);
    }

    public function test_usage_manager_can_be_resolved(): void
    {
        $manager = \Synetro\Fuse\Support\Facades\Fuse::usage('user', 'feature');
        $this->assertInstanceOf(\Synetro\Fuse\Usage\UsageManager::class, $manager);
    }

    public function test_quota_manager_can_be_resolved(): void
    {
        $manager = \Synetro\Fuse\Support\Facades\Fuse::quota('storage');
        $this->assertInstanceOf(\Synetro\Fuse\Usage\UsageManager::class, $manager);
    }

    public function test_discovery_manager_can_be_resolved(): void
    {
        $this->assertInstanceOf(\Synetro\Fuse\Discovery\DiscoveryManager::class, \Synetro\Fuse\Support\Facades\Fuse::auto());
    }
}
