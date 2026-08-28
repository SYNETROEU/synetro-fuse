<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Synetro\Fuse\Tests\TestCase;
use Synetro\Fuse\Config\ConfigManager;
use Synetro\Fuse\Secrets\SecretsManager;
use Synetro\Fuse\Features\FeatureManager;
use Synetro\Fuse\Cache\FuseCacheManager;
use Synetro\Fuse\Audit\AuditManager;
use Synetro\Fuse\Webhooks\WebhookManager;
use Synetro\Fuse\Resources\ResourceManager;
use Synetro\Fuse\Query\QueryManager;
use Synetro\Fuse\Actions\ActionManager;
use Synetro\Fuse\Health\HealthManager;
use Synetro\Fuse\Security\SecurityManager;
use Synetro\Fuse\Logging\LogManager;
use Synetro\Fuse\Metrics\MetricsManager;
use Synetro\Fuse\Notifications\NotificationManager;
use Synetro\Fuse\Files\FileManager;
use Synetro\Fuse\Api\ApiManager;
use Synetro\Fuse\Auth\AuthManager;
use Synetro\Fuse\Database\DatabaseManager;
use Synetro\Fuse\Pipeline\PipelineManager;

class CoreBindingsTest extends TestCase
{
    public function test_config_manager_is_bound(): void
    {
        $this->assertInstanceOf(ConfigManager::class, app(ConfigManager::class));
    }

    public function test_secrets_manager_is_bound(): void
    {
        $this->assertInstanceOf(SecretsManager::class, app(SecretsManager::class));
    }

    public function test_feature_manager_is_bound(): void
    {
        $this->assertInstanceOf(FeatureManager::class, app(FeatureManager::class));
    }

    public function test_cache_manager_is_bound(): void
    {
        $this->assertInstanceOf(FuseCacheManager::class, app(FuseCacheManager::class));
    }

    public function test_audit_manager_is_bound(): void
    {
        $this->assertInstanceOf(AuditManager::class, app(AuditManager::class));
    }

    public function test_webhook_manager_is_bound(): void
    {
        $this->assertInstanceOf(WebhookManager::class, app(WebhookManager::class));
    }

    public function test_resource_manager_is_bound(): void
    {
        $this->assertInstanceOf(ResourceManager::class, app(ResourceManager::class));
    }

    public function test_query_manager_is_bound(): void
    {
        $this->assertInstanceOf(QueryManager::class, app(QueryManager::class));
    }

    public function test_action_manager_is_bound(): void
    {
        $this->assertInstanceOf(ActionManager::class, app(ActionManager::class));
    }

    public function test_health_manager_is_bound(): void
    {
        $this->assertInstanceOf(HealthManager::class, app(HealthManager::class));
    }

    public function test_security_manager_is_bound(): void
    {
        $this->assertInstanceOf(SecurityManager::class, app(SecurityManager::class));
    }

    public function test_log_manager_is_bound(): void
    {
        $this->assertInstanceOf(LogManager::class, app(LogManager::class));
    }

    public function test_metrics_manager_is_bound(): void
    {
        $this->assertInstanceOf(MetricsManager::class, app(MetricsManager::class));
    }

    public function test_notification_manager_is_bound(): void
    {
        $this->assertInstanceOf(NotificationManager::class, app(NotificationManager::class));
    }

    public function test_file_manager_is_bound(): void
    {
        $this->assertInstanceOf(FileManager::class, app(FileManager::class));
    }

    public function test_api_manager_is_bound(): void
    {
        $this->assertInstanceOf(ApiManager::class, app(ApiManager::class));
    }

    public function test_auth_manager_is_bound(): void
    {
        $this->assertInstanceOf(AuthManager::class, app(AuthManager::class));
    }

    public function test_database_manager_is_bound(): void
    {
        $this->assertInstanceOf(DatabaseManager::class, app(DatabaseManager::class));
    }

    public function test_pipeline_manager_is_bound(): void
    {
        $this->assertInstanceOf(PipelineManager::class, app(PipelineManager::class));
    }

    public function test_fuse_class_is_bound(): void
    {
        $this->assertInstanceOf(\Synetro\Fuse\Support\Fuse::class, app(\Synetro\Fuse\Support\Fuse::class));
    }
}
