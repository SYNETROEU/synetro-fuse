<?php

declare(strict_types=1);

namespace Synetro\Fuse\Mail;

use Illuminate\Mail\Mailer;

class MailManager
{
    public function __construct(
        protected Mailer $mailer,
    ) {}

    public function send(mixed $to, string $template, array $data = []): void
    {
        $this->mailer->to($to)->send(new TemplateMailable($template, $data));
    }

    public function queue(mixed $to, string $template, array $data = []): void
    {
        $this->mailer->to($to)->queue(new TemplateMailable($template, $data));
    }

    public function later(mixed $to, \DateTimeInterface $delay, string $template, array $data = []): void
    {
        $this->mailer->to($to)->later($delay, new TemplateMailable($template, $data));
    }
}
