<?php

declare(strict_types=1);

namespace Synetro\Fuse\Exceptions;

class FuseException extends \Exception
{
    protected string $code = 'FUSE_ERROR';

    public static function code(string $code, string $message, int $status = 400): self
    {
        return (new self($message, $status))->setCode($code);
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getFuseCode(): string
    {
        return $this->code;
    }
}
