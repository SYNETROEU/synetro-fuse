<?php

declare(strict_types=1);

namespace Synetro\Fuse\Files;

use Illuminate\Filesystem\FilesystemAdapter;

class FileManager
{
    public function __construct(
        protected FilesystemAdapter $filesystem,
    ) {}

    public function disk(string $name): FilesystemAdapter
    {
        return Storage::disk($name);
    }

    public function put(string $path, mixed $contents, string $disk = null): bool
    {
        return $this->filesystem->put($path, $contents);
    }

    public function get(string $path, string $disk = null): string
    {
        return $this->filesystem->get($path);
    }

    public function delete(string $path, string $disk = null): bool
    {
        return $this->filesystem->delete($path);
    }

    public function exists(string $path, string $disk = null): bool
    {
        return $this->filesystem->exists($path);
    }

    public function url(string $path, string $disk = null): string
    {
        return $this->filesystem->url($path);
    }

    public function temporaryUrl(string $path, \DateTimeInterface $expiration, array $options = [], string $disk = null): string
    {
        return $this->filesystem->temporaryUrl($path, $expiration, $options);
    }
}
