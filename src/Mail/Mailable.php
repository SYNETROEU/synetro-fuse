<?php

declare(strict_types=1);

namespace Synetro\Fuse\Mail;

use Illuminate\Mail\Mailable;

class Mailable extends Mailable
{
    public function __construct(
        protected string $template,
        protected array $data = [],
    ) {}

    public function build(): self
    {
        return $this->view($this->template)->with($this->data);
    }
}
