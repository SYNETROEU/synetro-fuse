<?php

declare(strict_types=1);

namespace Synetro\Fuse\Validation;

use Illuminate\Contracts\Support\MessageBag;

class Validator
{
    public function __construct(protected mixed $data, protected array $rules) {}

    public function passes(): bool
    {
        $validator = \Illuminate\Support\Facades\Validator::make($this->data, $this->rules);

        return $validator->passes();
    }

    public function fails(): bool
    {
        return ! $this->passes();
    }

    public function validate(): void
    {
        $validator = \Illuminate\Support\Facades\Validator::make($this->data, $this->rules);

        $validator->validate();
    }

    public function errors(): MessageBag
    {
        $validator = \Illuminate\Support\Facades\Validator::make($this->data, $this->rules);

        return $validator->errors();
    }
}
