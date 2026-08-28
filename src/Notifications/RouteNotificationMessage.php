<?php

declare(strict_types=1);

namespace Synetro\Fuse\Notifications;

class RouteNotificationMessage
{
    public function __construct(
        protected string $channel,
        protected mixed $notifiable,
    ) {}

    public function send(mixed $notification): void
    {
        $this->notifiable->notify($notification);
    }
}
