<?php

declare(strict_types=1);

namespace Synetro\Fuse\Support;

use Illuminate\Contracts\Container\Container;
use Synetro\Fuse\Actions\ActionManager;
use Synetro\Fuse\Api\ApiManager;
use Synetro\Fuse\Auth\AuthManager;
use Synetro\Fuse\Bulk\BulkManager;
use Synetro\Fuse\Cache\FuseCacheManager;
use Synetro\Fuse\Config\ConfigManager;
use Synetro\Fuse\Console\ConsoleManager;
use Synetro\Fuse\Database\DatabaseManager;
use Synetro\Fuse\Discovery\DiscoveryManager;
use Synetro\Fuse\Exceptions\ExceptionsManager;
use Synetro\Fuse\Features\FeatureManager;
use Synetro\Fuse\Files\FileManager;
use Synetro\Fuse\Health\HealthManager;
use Synetro\Fuse\Http\HttpManager;
use Synetro\Fuse\Idempotency\IdempotencyManager;
use Synetro\Fuse\ImportExport\ImportExportManager;
use Synetro\Fuse\Locks\LockManager;
use Synetro\Fuse\Logging\LogManager;
use Synetro\Fuse\Mail\MailManager;
use Synetro\Fuse\Metrics\MetricsManager;
use Synetro\Fuse\Notifications\NotificationManager;
use Synetro\Fuse\Pipeline\PipelineManager;
use Synetro\Fuse\Profiling\ProfilerManager;
use Synetro\Fuse\Query\QueryManager;
use Synetro\Fuse\RateLimit\RateLimiter;
use Synetro\Fuse\Resources\ResourceManager;
use Synetro\Fuse\Security\SecurityManager;
use Synetro\Fuse\Usage\UsageManager;
use Synetro\Fuse\Validation\Validator;
use Synetro\Fuse\Webhooks\WebhookManager;
use Synetro\Fuse\Secrets\SecretsManager;
use Synetro\Fuse\Audit\AuditManager;
use Synetro\Fuse\Support\FuseExtensionManager;

class Fuse
{
    public function __construct(
        protected Container $app,
        protected FuseExtensionManager $extensions,
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

    public function feature(string $key): \Synetro\Fuse\Features\Feature
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

    public function validate(mixed $data, array $rules): Validator
    {
        return new Validator($data, $rules);
    }

    public function bulk(\Illuminate\Database\Query\Builder $query): BulkManager
    {
        return new BulkManager($query);
    }

    public function import(string $model, mixed $file, ?callable $onEach = null): array
    {
        return app(ImportExportManager::class)->import($model, $file, $onEach);
    }

    public function export(string $model, string $format = 'csv'): string
    {
        return app(ImportExportManager::class)->export($model, $format);
    }

    public function idempotent(string $key): IdempotencyManager
    {
        return app(IdempotencyManager::class)->for($key);
    }

    public function lock(string $name): LockManager
    {
        return app(LockManager::class)->for($name);
    }

    public function limit(string $name): RateLimiter
    {
        return app(RateLimiter::class)->for($name);
    }

    public function usage(string $user, string $feature): UsageManager
    {
        return app(UsageManager::class)->for($user, $feature);
    }

    public function quota(string $name): UsageManager
    {
        return app(UsageManager::class)->quota($name);
    }

    public function profile(callable $callback): array
    {
        $profiler = app(ProfilerManager::class);
        $profiler->start();

        $result = $callback();

        return $profiler->stop();
    }

    public function auto(): DiscoveryManager
    {
        return app(DiscoveryManager::class);
    }

    public function ai(): \Synetro\Fuse\Support\FuseAi
    {
        return app(\Synetro\Fuse\Support\FuseAi::class);
    }

    public function realtime(): \Synetro\Fuse\Support\FuseRealtime
    {
        return app(\Synetro\Fuse\Support\FuseRealtime::class);
    }

    public function broadcast(): \Synetro\Fuse\Support\FuseBroadcast
    {
        return app(\Synetro\Fuse\Support\FuseBroadcast::class);
    }

    public function payment(): \Synetro\Fuse\Support\FusePayment
    {
        return app(\Synetro\Fuse\Support\FusePayment::class);
    }

    public function subscription(): \Synetro\Fuse\Support\FuseSubscription
    {
        return app(\Synetro\Fuse\Support\FuseSubscription::class);
    }

    public function for(mixed $tenant): Fuse
    {
        return new FuseTenant($this->app, $tenant);
    }

    public function extend(string $name, callable $factory): void
    {
        $this->extensions->extend($name, $factory);
    }

    public function macro(string $name, callable $macro): void
    {
        $this->extensions->macro($name, $macro);
    }

    public function hasExtension(string $name): bool
    {
        return $this->extensions->has($name);
    }

    public function extension(string $name): ?callable
    {
        return $this->extensions->get($name);
    }

    public function extensions(): array
    {
        return $this->extensions->all();
    }

    public function registerHealthCheck(string $name, $check): void
    {
        $this->extensions->registerHealthCheck($name, $check);
    }

    public function registerDiscovery(string $type, string $class): void
    {
        $this->extensions->registerDiscovery($type, $class);
    }

    public function registerGeneratorStub(string $component, string $path): void
    {
        $this->extensions->registerGeneratorStub($component, $path);
    }

    public function subscribe(string $event, callable $listener): void
    {
        $this->extensions->subscribe($event, $listener);
    }
}
