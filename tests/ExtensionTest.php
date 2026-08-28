<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Synetro\Fuse\Health\HealthCheckInterface;
use Synetro\Fuse\Health\HealthResult;
use Synetro\Fuse\Support\Facades\Fuse;
use Synetro\Fuse\Support\FuseExtensionManager;

class ExtensionTest extends TestCase
{
    public function test_extend_registers_extension(): void
    {
        $manager = app(FuseExtensionManager::class);
        $manager->extend('custom', fn () => 'custom-value');

        $this->assertTrue($manager->has('custom'));
        $this->assertSame('custom-value', ($manager->get('custom'))());
    }

    public function test_macro_registers_macro(): void
    {
        $manager = app(FuseExtensionManager::class);
        $manager->macro('greet', fn (string $name) => "Hello, {$name}!");

        $this->assertTrue($manager->has('greet'));
        $this->assertSame('Hello, World!', ($manager->get('greet'))('World'));
    }

    public function test_extensions_returns_all(): void
    {
        $manager = app(FuseExtensionManager::class);
        $manager->extend('one', fn () => 1);
        $manager->macro('two', fn () => 2);

        $all = $manager->all();

        $this->assertArrayHasKey('one', $all);
        $this->assertArrayHasKey('two', $all);
    }

    public function test_register_health_check(): void
    {
        $manager = app(FuseExtensionManager::class);
        $check = new class implements HealthCheckInterface
        {
            public function check(): HealthResult
            {
                return HealthResult::pass('custom');
            }
        };

        $manager->registerHealthCheck('custom', $check);

        $this->assertArrayHasKey('custom', $manager->healthChecks());
    }

    public function test_register_discovery(): void
    {
        $manager = app(FuseExtensionManager::class);
        $manager->registerDiscovery('Actions', 'App\\Custom\\MyAction');

        $this->assertContains('App\\Custom\\MyAction', $manager->discoveryClasses('Actions'));
    }

    public function test_register_generator_stub(): void
    {
        $manager = app(FuseExtensionManager::class);
        $manager->registerGeneratorStub('controller', '/custom/stubs/controller.stub');

        $this->assertSame('/custom/stubs/controller.stub', $manager->generatorStubs()['controller']);
    }

    public function test_subscribe_registers_event_listener(): void
    {
        $manager = app(FuseExtensionManager::class);
        $listener = fn ($event) => true;
        $manager->subscribe('order.created', $listener);

        $this->assertCount(1, $manager->eventSubscribers()['order.created']);
    }

    public function test_fuse_facade_can_extend(): void
    {
        Fuse::extend('facade-test', fn () => 'works');

        $this->assertTrue(Fuse::hasExtension('facade-test'));
    }
}
