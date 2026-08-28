<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Synetro\Fuse\Config\ConfigManager;

class ConfigManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'sqlite'])->run();
    }

    public function test_config_can_be_set_and_retrieved(): void
    {
        $manager = app(ConfigManager::class);
        $manager->set('test.key', 'value');

        $this->assertSame('value', $manager->get('test.key'));
    }

    public function test_config_returns_default_when_missing(): void
    {
        $manager = app(ConfigManager::class);

        $this->assertSame('default', $manager->get('missing.key', 'default'));
    }

    public function test_config_can_be_deleted(): void
    {
        $manager = app(ConfigManager::class);
        $manager->set('test.delete', 'value');
        $manager->delete('test.delete');

        $this->assertNull($manager->get('test.delete'));
    }
}
