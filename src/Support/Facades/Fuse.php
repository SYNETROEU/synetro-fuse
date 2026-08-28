<?php

declare(strict_types=1);

namespace Synetro\Fuse\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Synetro\Fuse\Resources\ResourceManager resource(string $model)
 * @method static mixed config(string|null $key = null, mixed $default = null)
 * @method static \Synetro\Fuse\Secrets\SecretsManager secret(string $key)
 * @method static \Synetro\Fuse\Features\FeatureManager feature(string $key)
 * @method static \Synetro\Fuse\Health\HealthManager health()
 * @method static \Synetro\Fuse\Webhooks\WebhookManager webhook(string $name)
 * @method static mixed cache(string $key, callable|null $callback = null)
 * @method static mixed cacheFor(string $key, int $seconds, callable $callback)
 * @method static void forget(string $key)
 * @method static \Synetro\Fuse\Audit\AuditManager audit()
 * @method static \Synetro\Fuse\Auth\AuthManager auth()
 * @method static \Synetro\Fuse\Api\ApiManager api()
 * @method static \Synetro\Fuse\Security\SecurityManager security()
 * @method static \Synetro\Fuse\Metrics\MetricsManager metrics()
 * @method static \Synetro\Fuse\Database\DatabaseManager database()
 * @method static \Synetro\Fuse\Pipeline\PipelineManager pipeline(array $steps)
 * @method static \Synetro\Fuse\Logging\LogManager log()
 * @method static \Synetro\Fuse\Mail\MailManager mail()
 * @method static \Synetro\Fuse\Query\QueryManager query(string $model)
 * @method static \Synetro\Fuse\Files\FileManager file()
 * @method static \Synetro\Fuse\Notifications\NotificationManager notify()
 * @method static \Synetro\Fuse\Http\HttpManager http()
 * @method static \Synetro\Fuse\Validation\Validator validate(mixed $data, array $rules)
 * @method static \Synetro\Fuse\Bulk\BulkManager bulk(\Illuminate\Database\Query\Builder $query)
 * @method static array import(string $model, mixed $file, callable|null $onEach = null)
 * @method static string export(string $model, string $format = 'csv')
 * @method static \Synetro\Fuse\Idempotency\IdempotencyManager idempotent(string $key)
 * @method static \Synetro\Fuse\Locks\LockManager lock(string $name)
 * @method static \Synetro\Fuse\RateLimit\RateLimiter limit(string $name)
 * @method static \Synetro\Fuse\Usage\UsageManager usage(string $user, string $feature)
 * @method static \Synetro\Fuse\Usage\UsageManager quota(string $name)
 * @method static array profile(callable $callback)
 * @method static \Synetro\Fuse\Discovery\DiscoveryManager auto()
 * @method static \Synetro\Fuse\Support\FuseAi ai()
 * @method static \Synetro\Fuse\Support\FuseRealtime realtime()
 * @method static \Synetro\Fuse\Support\FuseBroadcast broadcast()
 * @method static \Synetro\Fuse\Support\FusePayment payment()
 * @method static \Synetro\Fuse\Support\FuseSubscription subscription()
 * @method static \Synetro\Fuse\Support\Fuse for(mixed $tenant)
 *
 * @see \Synetro\Fuse\Support\Fuse
 */
class Fuse extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Synetro\Fuse\Support\Fuse::class;
    }
}
