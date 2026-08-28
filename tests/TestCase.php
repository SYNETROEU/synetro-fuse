<?php

declare(strict_types=1);

namespace Synetro\Fuse\Testing;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Synetro\Fuse\FuseServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            FuseServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Fuse' => \Synetro\Fuse\Support\Facades\Fuse::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }
}
