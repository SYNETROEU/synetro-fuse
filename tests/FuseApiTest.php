<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Synetro\Fuse\Api\ApiManager;
use Synetro\Fuse\Auth\AuthManager;
use Synetro\Fuse\Discovery\DiscoveryManager;
use Synetro\Fuse\Features\Feature;
use Synetro\Fuse\Health\HealthManager;
use Synetro\Fuse\Idempotency\IdempotencyManager;
use Synetro\Fuse\Locks\LockManager;
use Synetro\Fuse\Logging\LogManager;
use Synetro\Fuse\Metrics\MetricsManager;
use Synetro\Fuse\Notifications\NotificationManager;
use Synetro\Fuse\RateLimit\RateLimiter;
use Synetro\Fuse\Resources\ResourceBuilder;
use Synetro\Fuse\Resources\ResourceQuery;
use Synetro\Fuse\Security\SecurityManager;
use Synetro\Fuse\Support\Facades\Fuse;
use Synetro\Fuse\Tests\Resources\Stubs\Product;
use Synetro\Fuse\Usage\UsageManager;
use Synetro\Fuse\Validation\Validator;

class FuseApiTest extends TestCase
{
    public function test_resource_builder_can_be_created(): void
    {
        $manager = Fuse::resource(Product::class);

        $this->assertInstanceOf(ResourceBuilder::class, $manager);
    }

    public function test_resource_query_can_be_built(): void
    {
        $query = Fuse::resource(Product::class)
            ->search(['name'])
            ->buildQuery();

        $this->assertInstanceOf(ResourceQuery::class, $query);
    }

    public function test_feature_manager_can_be_resolved(): void
    {
        $this->assertInstanceOf(Feature::class, Fuse::feature('test'));
    }

    public function test_cache_manager_can_be_resolved(): void
    {
        $result = Fuse::cacheFor('test', 60, fn () => 'cached');
        $this->assertSame('cached', $result);
    }

    public function test_health_manager_can_be_resolved(): void
    {
        $this->assertInstanceOf(HealthManager::class, Fuse::health());
    }

    public function test_security_manager_can_be_resolved(): void
    {
        $this->assertInstanceOf(SecurityManager::class, Fuse::security());
    }

    public function test_log_manager_can_be_resolved(): void
    {
        $this->assertInstanceOf(LogManager::class, Fuse::log());
    }

    public function test_metrics_manager_can_be_resolved(): void
    {
        $this->assertInstanceOf(MetricsManager::class, Fuse::metrics());
    }

    public function test_notification_manager_can_be_resolved(): void
    {
        $this->assertInstanceOf(NotificationManager::class, Fuse::notify());
    }

    public function test_api_manager_can_be_resolved(): void
    {
        $this->assertInstanceOf(ApiManager::class, Fuse::api());
    }

    public function test_auth_manager_can_be_resolved(): void
    {
        $this->assertInstanceOf(AuthManager::class, Fuse::auth());
    }

    public function test_validation_manager_can_be_resolved(): void
    {
        $validator = Fuse::validate([], []);
        $this->assertInstanceOf(Validator::class, $validator);
    }

    public function test_idempotency_manager_can_be_resolved(): void
    {
        $manager = Fuse::idempotent('key');
        $this->assertInstanceOf(IdempotencyManager::class, $manager);
    }

    public function test_lock_manager_can_be_resolved(): void
    {
        $manager = Fuse::lock('name');
        $this->assertInstanceOf(LockManager::class, $manager);
    }

    public function test_rate_limiter_can_be_resolved(): void
    {
        $limiter = Fuse::limit('name');
        $this->assertInstanceOf(RateLimiter::class, $limiter);
    }

    public function test_usage_manager_can_be_resolved(): void
    {
        $manager = Fuse::usage('user', 'feature');
        $this->assertInstanceOf(UsageManager::class, $manager);
    }

    public function test_quota_manager_can_be_resolved(): void
    {
        $manager = Fuse::quota('storage');
        $this->assertInstanceOf(UsageManager::class, $manager);
    }

    public function test_discovery_manager_can_be_resolved(): void
    {
        $this->assertInstanceOf(DiscoveryManager::class, Fuse::auto());
    }
}
