<?php

declare(strict_types=1);

namespace Synetro\Fuse\Discovery;

class DiscoveryManager
{
    protected array $discovered = [];
    protected bool $enabled = true;

    public function discover(string $type, string $namespace): array
    {
        if (!$this->enabled) {
            return [];
        }

        $cacheKey = "fuse.discovery.{$type}";

        if ($this->discovered[$type] ?? false) {
            return $this->discovered[$type];
        }

        $cached = \Illuminate\Support\Facades\Cache::get($cacheKey);

        if ($cached !== null) {
            return $this->discovered[$type] = $cached;
        }

        $results = $this->scanNamespace($namespace);

        \Illuminate\Support\Facades\Cache::put($cacheKey, $results, 3600);

        return $this->discovered[$type] = $results;
    }

    public function auto(): array
    {
        $results = [];

        $types = ['Actions', 'Policies', 'Resources', 'Commands', 'Events', 'Webhooks', 'Settings', 'Feature', 'Health'];

        foreach ($types as $type) {
            $namespace = "App\\{$type}";
            $results[$type] = $this->discover($type, $namespace);
        }

        return $results;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    protected function scanNamespace(string $namespace): array
    {
        $path = app_path(str_replace('App\\', '', $namespace));

        if (!is_dir($path)) {
            return [];
        }

        $files = glob($path . '/*.php');
        $classes = [];

        foreach ($files as $file) {
            $class = $namespace . '\\' . pathinfo($file, PATHINFO_FILENAME);
            if (class_exists($class)) {
                $classes[] = $class;
            }
        }

        return $classes;
    }
}
