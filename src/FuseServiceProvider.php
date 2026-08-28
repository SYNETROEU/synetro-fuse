<?php

declare(strict_types=1);

namespace Synetro\Fuse;

use Illuminate\Support\ServiceProvider;
use Synetro\Fuse\Config\ConfigManager;
use Synetro\Fuse\Features\FeatureManager;
use Synetro\Fuse\Secrets\SecretsManager;
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
use Synetro\Fuse\Console\InstallCommand;
use Synetro\Fuse\Console\DoctorCommand;
use Synetro\Fuse\Console\OptimizeCommand;
use Synetro\Fuse\Console\AboutCommand;
use Synetro\Fuse\Console\GenerateCommand;
use Synetro\Fuse\Console\DatabaseCommand;
use Synetro\Fuse\Console\RoutesCommand;
use Synetro\Fuse\Console\ModelsCommand;
use Synetro\Fuse\Console\EventsCommand;
use Synetro\Fuse\Console\JobsCommand;
use Synetro\Fuse\Console\OpenApiCommand;
use Synetro\Fuse\Console\DocsCommand;
use Synetro\Fuse\Console\InspectCommand;
use Synetro\Fuse\Console\SecurityCommand;
use Synetro\Fuse\Console\FuseAuthCommand;
use Synetro\Fuse\Console\CleanupCommand;
use Synetro\Fuse\Support\FuseExtensionManager;
use Synetro\Fuse\Validation\Validator;
use Synetro\Fuse\Bulk\BulkManager;
use Synetro\Fuse\ImportExport\ImportExportManager;
use Synetro\Fuse\Idempotency\IdempotencyManager;
use Synetro\Fuse\Locks\LockManager;
use Synetro\Fuse\RateLimit\RateLimiter;
use Synetro\Fuse\Usage\UsageManager;
use Synetro\Fuse\Profiling\ProfilerManager;
use Synetro\Fuse\Discovery\DiscoveryManager;

class FuseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/fuse.php',
            'fuse'
        );

        $this->registerCoreBindings();
        $this->registerFacades();
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                DoctorCommand::class,
                OptimizeCommand::class,
                AboutCommand::class,
                GenerateCommand::class,
                DatabaseCommand::class,
                RoutesCommand::class,
                ModelsCommand::class,
                EventsCommand::class,
                JobsCommand::class,
                OpenApiCommand::class,
                DocsCommand::class,
                InspectCommand::class,
                SecurityCommand::class,
                FuseAuthCommand::class,
                CleanupCommand::class,
            ]);
        }

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if (config('fuse.routes.enabled', true)) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        }

        if (config('fuse.middleware.auto_register', true)) {
            $this->app['router']->aliasMiddleware('fuse', \Synetro\Fuse\Http\Middleware\FuseMiddleware::class);
            $this->app['router']->aliasMiddleware('fuse.auth', \Synetro\Fuse\Http\Middleware\FuseAuthMiddleware::class);
            $this->app['router']->aliasMiddleware('fuse.throttle', \Synetro\Fuse\Http\Middleware\FuseThrottleMiddleware::class);
        }
    }

    protected function registerCoreBindings(): void
    {
        $this->app->singleton(ConfigManager::class, function ($app) {
            return new ConfigManager($app['config'], $app['cache.store'], $app['db']->connection());
        });

        $this->app->singleton(SecretsManager::class, function ($app) {
            return new SecretsManager($app['config'], $app['encrypter'], $app['cache.store']);
        });

        $this->app->singleton(FeatureManager::class, function ($app) {
            return new FeatureManager($app[ConfigManager::class], $app['cache.store']);
        });

        $this->app->singleton(FuseCacheManager::class, function ($app) {
            return new FuseCacheManager($app['cache.store']);
        });

        $this->app->singleton(AuditManager::class, function ($app) {
            return new AuditManager($app['db']->connection(), $app['events']);
        });

        $this->app->singleton(WebhookManager::class, function ($app) {
            return new WebhookManager($app['config'], \Illuminate\Support\Facades\Http::getFacadeRoot());
        });

        $this->app->singleton(ResourceManager::class, function ($app) {
            return new ResourceManager($app['router'], $app['auth']);
        });

        $this->app->singleton(QueryManager::class, function ($app) {
            return new QueryManager();
        });

        $this->app->singleton(ActionManager::class, function ($app) {
            return new ActionManager($app['events'], $app['queue']->connection());
        });

        $this->app->singleton(HealthManager::class, function ($app) {
            return new HealthManager($app['db']->connection(), $app['cache.store'], $app['queue']->connection(), $app['filesystem']->disk());
        });

        $this->app->singleton(SecurityManager::class, function ($app) {
            return new SecurityManager($app['request']);
        });

        $this->app->singleton(LogManager::class, function ($app) {
            return new LogManager($app['log']);
        });

        $this->app->singleton(MetricsManager::class, function ($app) {
            return new MetricsManager($app['cache.store']);
        });

        $this->app->singleton(NotificationManager::class, function ($app) {
            return new NotificationManager($app['auth']);
        });

        $this->app->singleton(FileManager::class, function ($app) {
            return new FileManager($app['filesystem']->disk());
        });

        $this->app->singleton(FuseExtensionManager::class, function ($app) {
            return new FuseExtensionManager($app);
        });

        $this->app->singleton(\Synetro\Fuse\Support\Fuse::class, function ($app) {
            return new \Synetro\Fuse\Support\Fuse($app, $app[FuseExtensionManager::class]);
        });

        $this->app->singleton(Validator::class, function ($app) {
            return new Validator([], []);
        });

        $this->app->singleton(BulkManager::class, function ($app) {
            return new BulkManager(\Illuminate\Database\Query\Builder::query(\Illuminate\Support\Facades\DB::connection()));
        });

        $this->app->singleton(ImportExportManager::class, function ($app) {
            return new ImportExportManager();
        });

        $this->app->singleton(IdempotencyManager::class, function ($app) {
            return new IdempotencyManager($app['cache.store']);
        });

        $this->app->singleton(LockManager::class, function ($app) {
            return new LockManager($app['cache.store']);
        });

        $this->app->singleton(RateLimiter::class, function ($app) {
            return new RateLimiter($app['cache.store']);
        });

        $this->app->singleton(UsageManager::class, function ($app) {
            return new UsageManager($app['cache.store']);
        });

        $this->app->singleton(ProfilerManager::class, function ($app) {
            return new ProfilerManager();
        });

        $this->app->singleton(DiscoveryManager::class, function ($app) {
            return new DiscoveryManager();
        });
    }

    protected function registerFacades(): void
    {
        if (class_exists(\Illuminate\Support\Facades\Facade::class)) {
            if (!class_exists('Fuse', false)) {
                class_alias(\Synetro\Fuse\Support\Facades\Fuse::class, 'Fuse');
            }
        }
    }
}
