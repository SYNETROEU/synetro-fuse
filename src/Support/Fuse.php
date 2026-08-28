<?php

declare(strict_types=1);

namespace Synetro\Fuse\Support;

use Illuminate\Contracts\Container\Container;
use Synetro\Fuse\Actions\ActionManager;
use Synetro\Fuse\Api\ApiManager;
use Synetro\Fuse\Auth\AuthManager;
use Synetro\Fuse\Cache\FuseCacheManager;
use Synetro\Fuse\Config\ConfigManager;
use Synetro\Fuse\Console\ConsoleManager;
use Synetro\Fuse\Database\DatabaseManager;
use Synetro\Fuse\Features\FeatureManager;
use Synetro\Fuse\Files\FileManager;
use Synetro\Fuse\Health\HealthManager;
use Synetro\Fuse\Http\HttpManager;
use Synetro\Fuse\Logging\LogManager;
use Synetro\Fuse\Mail\MailManager;
use Synetro\Fuse\Metrics\MetricsManager;
use Synetro\Fuse\Notifications\NotificationManager;
use Synetro\Fuse\Pipeline\PipelineManager;
use Synetro\Fuse\Query\QueryManager;
use Synetro\Fuse\Resources\ResourceManager;
use Synetro\Fuse\Security\SecurityManager;
use Synetro\Fuse\Webhooks\WebhookManager;
use Synetro\Fuse\Secrets\SecretsManager;
use Synetro\Fuse\Audit\AuditManager;

class Fuse
{
    public function __construct(
        protected Container $app,
    ) {}

    public function resource(string $model): ResourceManager
    {
        return app(ResourceManager::class)->for($model);
    }

    public function config(?string $key = null, mixed $default = null): mixed
    {
        return app(ConfigManager::class)->get($key, $default);
    }

    public function secret(string $key): SecretsManager
    {
        return app(SecretsManager::class)->for($key);
    }

    public function feature(string $key): FeatureManager
    {
        return app(FeatureManager::class)->for($key);
    }

    public function health(): HealthManager
    {
        return app(HealthManager::class);
    }

    public function webhook(string $name): WebhookManager
    {
        return app(WebhookManager::class)->for($name);
    }

    public function cache(string $key, ?callable $callback = null): mixed
    {
        return app(FuseCacheManager::class)->remember($key, $callback);
    }

    public function cacheFor(string $key, int $seconds, callable $callback): mixed
    {
        return app(FuseCacheManager::class)->remember($key, $callback, $seconds);
    }

    public function forget(string $key): void
    {
        app(FuseCacheManager::class)->forget($key);
    }

    public function audit(): AuditManager
    {
        return app(AuditManager::class);
    }

    public function auth(): AuthManager
    {
        return app(AuthManager::class);
    }

    public function api(): ApiManager
    {
        return app(ApiManager::class);
    }

    public function security(): SecurityManager
    {
        return app(SecurityManager::class);
    }

    public function metrics(): MetricsManager
    {
        return app(MetricsManager::class);
    }

    public function database(): DatabaseManager
    {
        return app(DatabaseManager::class);
    }

    public function pipeline(array $steps): PipelineManager
    {
        return app(PipelineManager::class)->steps($steps);
    }

    public function log(): LogManager
    {
        return app(LogManager::class);
    }

    public function mail(): MailManager
    {
        return app(MailManager::class);
    }

    public function query(string $model): QueryManager
    {
        return app(QueryManager::class)->for($model);
    }

    public function file(): FileManager
    {
        return app(FileManager::class);
    }

    public function notify(): NotificationManager
    {
        return app(NotificationManager::class);
    }

    public function http(): HttpManager
    {
        return app(HttpManager::class);
    }

    public function for(mixed $tenant): Fuse
    {
        return new FuseTenant($this->app, $tenant);
    }
}
