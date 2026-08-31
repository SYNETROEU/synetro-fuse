<?php

declare(strict_types=1);

namespace Synetro\Fuse\Config;

use Illuminate\Support\Collection;

use Illuminate\Cache\Repository;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Database\Connection;
use Synetro\Fuse\Models\Config as ConfigModel;

class ConfigManager
{
    public function __construct(
        protected ConfigRepository $config,
        protected ?Repository $cache,
        protected Connection $db,
    ) {}

    public function get(string $key, mixed $default = null): mixed
    {
        $cacheKey = "fuse.config.{$key}";

        if ($this->cache && config('fuse.config.cache')) {
            return $this->cache->remember($cacheKey, config('fuse.cache.ttl', 3600), function () use ($key, $default) {
                return $this->resolve($key, $default);
            });
        }

        return $this->resolve($key, $default);
    }

    public function set(string $key, mixed $value): void
    {
        $this->db->table('fuse_configs')->updateOrInsert(
            ['key' => $key],
            ['value' => $this->serialize($value), 'updated_at' => now()]
        );

        $this->invalidate($key);
    }

    public function delete(string $key): void
    {
        $this->db->table('fuse_configs')->where('key', $key)->delete();
        $this->invalidate($key);
    }

    public function all(): Collection
    {
        // Use lazy collection to prevent memory exhaustion with large datasets
        return ConfigModel::query()->lazy()->mapWithKeys(fn ($item) => [
            $item->key => $this->unserialize($item->value),
        ])->collect();
    }

    public function publish(): bool
    {
        $target = config_path('fuse.php');

        if (file_exists($target)) {
            return false;
        }

        try {
            $content = file_get_contents(__DIR__.'/../../config/fuse.php');
            if ($content === false) {
                return false;
            }

            return file_put_contents($target, $content) !== false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function resolve(string $key, mixed $default): mixed
    {
        if (config('fuse.config.driver') === 'database') {
            try {
                $item = ConfigModel::where('key', $key)->first();

                if ($item) {
                    return $this->unserialize($item->value);
                }
            } catch (\Throwable $e) {
                // Table may not exist yet; fall back to Laravel config
            }
        }

        return $this->config->get("fuse.{$key}", $default);
    }

    protected function invalidate(string $key): void
    {
        if ($this->cache && config('fuse.config.cache')) {
            $this->cache->forget("fuse.config.{$key}");
        }
    }

    protected function serialize(mixed $value): string
    {
        return serialize($value);
    }

    protected function unserialize(string $value): mixed
    {
        return @unserialize($value) ?: $value;
    }
}
