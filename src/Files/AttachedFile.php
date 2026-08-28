<?php

declare(strict_types=1);

namespace Synetro\Fuse\Files;

class AttachedFile
{
    public function __construct(
        public string $path,
        public string $disk,
        public string $url,
    ) {}

    public function url(): string
    {
        return $this->url;
    }

    public function temporaryUrl(\DateTimeInterface $expiration, array $options = []): string
    {
        return Storage::disk($this->disk)->temporaryUrl($this->path, $expiration, $options);
    }

    public function delete(): bool
    {
        return Storage::disk($this->disk)->delete($this->path);
    }
}
