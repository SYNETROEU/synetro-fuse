<?php

declare(strict_types=1);

namespace Synetro\Fuse\Secrets;

use Illuminate\Cache\Repository;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Encryption\Encrypter;

class SecretsManager
{
    public function __construct(
        protected ConfigRepository $config,
        protected Encrypter $encrypter,
        protected ?Repository $cache,
    ) {}

    public function for(string $key): Secret
    {
        return new Secret($key, $this);
    }

    public function get(string $key): ?string
    {
        $value = env($key);

        if ($value !== null) {
            return $value;
        }

        $cacheKey = "fuse.secret.{$key}";

        if ($this->cache && config('fuse.secrets.cache', true)) {
            return $this->cache->remember($cacheKey, 3600, function () use ($key) {
                return $this->resolveFromDatabase($key);
            });
        }

        return $this->resolveFromDatabase($key);
    }

    public function set(string $key, string $value): void
    {
        $encrypted = $this->encrypter->encrypt($value);

        $this->cache?->forget("fuse.secret.{$key}");

        // Store in database (would need a secrets table)
        // For now, store in encrypted file
        $this->storeToFile($key, $encrypted);
    }

    public function delete(string $key): void
    {
        $this->cache?->forget("fuse.secret.{$key}");

        if (file_exists($this->secretPath($key))) {
            unlink($this->secretPath($key));
        }
    }

    public function redact(string $value): string
    {
        if (strlen($value) <= 8) {
            return str_repeat('*', strlen($value));
        }

        return substr($value, 0, 4).str_repeat('*', strlen($value) - 8).substr($value, -4);
    }

    protected function resolveFromDatabase(string $key): ?string
    {
        $path = $this->secretPath($key);

        if (! file_exists($path)) {
            return null;
        }

        $encrypted = file_get_contents($path);

        return $this->encrypter->decrypt($encrypted);
    }

    protected function storeToFile(string $key, string $encrypted): void
    {
        $path = $this->secretPath($key);

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0700, true);
        }

        file_put_contents($path, $encrypted);
    }

    protected function secretPath(string $key): string
    {
        $hash = hash('sha256', $key);

        return storage_path('app/fuse/secrets/'.$hash);
    }
}
