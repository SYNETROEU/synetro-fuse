<?php

declare(strict_types=1);

namespace Synetro\Fuse\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Synetro\Fuse\Audit\Audit;
use Synetro\Fuse\Files\AttachedFile;
use Synetro\Fuse\Query\QueryManager;

trait HasFuse
{
    public function publicId(): string
    {
        return (string) Str::ulid();
    }

    public function activity()
    {
        return $this->morphMany(Audit::class, 'actor');
    }

    public function attachFile(string $name, mixed $file): AttachedFile
    {
        $path = $this->getTable().'/'.$this->getKey().'/'.$name;
        $disk = config('fuse.files.default_disk', 'public');

        Storage::disk($disk)->put($path, $file);

        return new AttachedFile($path, $disk, Storage::disk($disk)->url($path));
    }

    public function file(string $name): ?AttachedFile
    {
        $path = $this->getTable().'/'.$this->getKey().'/'.$name;
        $disk = config('fuse.files.default_disk', 'public');

        if (! Storage::disk($disk)->exists($path)) {
            return null;
        }

        return new AttachedFile($path, $disk, Storage::disk($disk)->url($path));
    }

    public function fuse(): QueryManager
    {
        return app(QueryManager::class)->for($this->getTable());
    }

    public function cached(): self
    {
        return $this->remember($this->getTable().':'.$this->getKey(), 3600);
    }

    public function remember(string $key, int $seconds): self
    {
        return cache()->remember($key, $seconds, fn () => $this);
    }
}
