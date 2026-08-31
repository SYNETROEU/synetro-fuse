<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Synetro\Fuse\Config\ConfigManager;
use Synetro\Fuse\Models\Config as ConfigModel;

class ConfigManagerAdvancedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--database' => 'sqlite'])->run();
    }

    public function test_config_all_returns_collection(): void
    {
        $manager = app(ConfigManager::class);
        
        $manager->set('app.name', 'Test App');
        $manager->set('app.version', '1.0.0');

        $all = $manager->all();

        $this->assertIsObject($all);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $all);
    }

    public function test_config_all_loads_all_keys(): void
    {
        $manager = app(ConfigManager::class);
        
        $manager->set('test.key1', 'value1');
        $manager->set('test.key2', 'value2');
        $manager->set('test.key3', 'value3');

        $all = $manager->all();

        $this->assertCount(3, $all);
        $this->assertEquals('value1', $all['test.key1']);
        $this->assertEquals('value2', $all['test.key2']);
    }

    public function test_config_publish_handles_file_errors(): void
    {
        $manager = app(ConfigManager::class);

        // Publish should handle file operations safely
        $result = $manager->publish();
        
        // Result should be a boolean
        $this->assertIsBool($result);
    }

    public function test_config_publish_returns_false_on_existing(): void
    {
        $manager = app(ConfigManager::class);

        // Create the target file
        $target = config_path('fuse.php');
        if (!file_exists($target)) {
            touch($target);
        }

        try {
            $result = $manager->publish();
            $this->assertFalse($result);
        } finally {
            if (file_exists($target)) {
                unlink($target);
            }
        }
    }

    public function test_config_all_with_large_dataset(): void
    {
        $manager = app(ConfigManager::class);

        // Add many config entries
        for ($i = 0; $i < 100; $i++) {
            $manager->set("config.item.{$i}", "value{$i}");
        }

        // all() should not cause memory issues with lazy loading
        $all = $manager->all();
        $this->assertCount(100, $all);
    }

    public function test_config_serialization_preserves_types(): void
    {
        $manager = app(ConfigManager::class);

        $testData = [
            'string' => 'test',
            'integer' => 42,
            'float' => 3.14,
            'boolean' => true,
            'array' => [1, 2, 3],
            'nested' => ['key' => 'value'],
        ];

        $manager->set('complex.data', $testData);
        $retrieved = $manager->get('complex.data');

        $this->assertEquals($testData, $retrieved);
        $this->assertIsArray($retrieved['array']);
        $this->assertTrue($retrieved['boolean']);
    }

    public function test_config_cache_invalidation_on_set(): void
    {
        $manager = app(ConfigManager::class);

        // Set and cache a value
        $manager->set('cache.test', 'original');
        $cached1 = $manager->get('cache.test');
        $this->assertEquals('original', $cached1);

        // Update the value
        $manager->set('cache.test', 'updated');
        $cached2 = $manager->get('cache.test');

        // Should get updated value, not cached old value
        $this->assertEquals('updated', $cached2);
    }

    public function test_config_cache_invalidation_on_delete(): void
    {
        $manager = app(ConfigManager::class);

        // Set and cache a value
        $manager->set('cache.delete.test', 'value');
        $initial = $manager->get('cache.delete.test');
        $this->assertEquals('value', $initial);

        // Delete it
        $manager->delete('cache.delete.test');
        $after = $manager->get('cache.delete.test');

        // Should be null after deletion, not cached old value
        $this->assertNull($after);
    }
}
