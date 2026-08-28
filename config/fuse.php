<?php

declare(strict_types=1);
use Synetro\Fuse\Features\FeatureServiceProvider;
use Synetro\Fuse\Health\Checks\CacheCheck;
use Synetro\Fuse\Health\Checks\DatabaseCheck;
use Synetro\Fuse\Health\Checks\QueueCheck;
use Synetro\Fuse\Health\Checks\StorageCheck;
use Synetro\Fuse\Webhooks\WebhookServiceProvider;

return [
    'name' => env('FUSE_APP_NAME', 'Fuse'),

    'providers' => [
        FeatureServiceProvider::class,
        WebhookServiceProvider::class,
    ],

    'routes' => [
        'enabled' => env('FUSE_ROUTES', true),
        'prefix' => env('FUSE_ROUTE_PREFIX', 'fuse'),
        'middleware' => ['api'],
    ],

    'middleware' => [
        'auto_register' => env('FUSE_MIDDLEWARE_AUTO_REGISTER', true),
    ],

    'cache' => [
        'enabled' => env('FUSE_CACHE_ENABLED', true),
        'store' => env('FUSE_CACHE_STORE', null),
        'ttl' => (int) env('FUSE_CACHE_TTL', 3600),
    ],

    'config' => [
        'cache' => true,
        'driver' => env('FUSE_CONFIG_DRIVER', 'database'),
    ],

    'features' => [
        'enabled' => env('FUSE_FEATURES_ENABLED', true),
        'cache' => true,
        'ttl' => 300,
    ],

    'secrets' => [
        'encryption' => true,
        'redact_from_logs' => true,
    ],

    'audit' => [
        'enabled' => env('FUSE_AUDIT_ENABLED', true),
        'queue' => env('FUSE_AUDIT_QUEUE', false),
        'connection' => env('FUSE_AUDIT_CONNECTION', null),
        'exclude' => [
            'password',
            'password_confirmation',
            'remember_token',
            'api_token',
            'secret',
        ],
    ],

    'webhooks' => [
        'enabled' => env('FUSE_WEBHOOKS_ENABLED', true),
        'signature_header' => 'X-Fuse-Signature',
        'timestamp_tolerance' => 300,
        'queue' => env('FUSE_WEBHOOK_QUEUE', false),
        'retry' => [
            'times' => 3,
            'delay' => [60, 120, 300],
        ],
    ],

    'api' => [
        'envelope' => true,
        'prefix' => env('FUSE_API_PREFIX', 'api'),
    ],

    'security' => [
        'headers' => [
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
        ],
    ],

    'health' => [
        'enabled' => env('FUSE_HEALTH_ENABLED', true),
        'prefix' => '_health',
        'checks' => [
            DatabaseCheck::class,
            CacheCheck::class,
            QueueCheck::class,
            StorageCheck::class,
        ],
    ],

    'generators' => [
        'namespace' => 'App\\Features',
    ],

    'validation' => [
        'enabled' => true,
    ],

    'bulk' => [
        'chunk_size' => 1000,
        'authorize' => true,
    ],

    'import_export' => [
        'chunk_size' => 1000,
    ],

    'idempotency' => [
        'ttl' => 3600,
    ],

    'locks' => [
        'timeout' => 60,
    ],

    'rate_limit' => [
        'enabled' => true,
    ],

    'usage' => [
        'enabled' => true,
    ],

    'profiling' => [
        'enabled' => true,
    ],

    'discovery' => [
        'enabled' => true,
        'cache_ttl' => 3600,
    ],
];
