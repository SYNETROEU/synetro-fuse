<?php

declare(strict_types=1);

namespace Synetro\Fuse\Notifications;

use Illuminate\Notifications\Notification;

class Notification extends Notification
{
    public function __construct(
        protected string $message,
    ) {}

    public function via(mixed $notifiable): array
    {
        return ['database'];
    }

    public function toArray(mixed $notifiable): array
    {
        return ['message' => $this->message];
    }
}
