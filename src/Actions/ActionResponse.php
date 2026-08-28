<?php

declare(strict_types=1);

namespace Synetro\Fuse\Actions;

class ActionResponse
{
    public function __construct(
        public mixed $result,
        public bool $success = true,
        public ?string $error = null,
    ) {}

    public static function success(mixed $result): self
    {
        return new self($result, true);
    }

    public static function failed(string $error): self
    {
        return new self(null, false, $error);
    }
}
