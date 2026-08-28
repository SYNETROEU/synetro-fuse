<?php

declare(strict_types=1);

namespace Synetro\Fuse\Notifications;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

class NotificationManager
{
    public function __construct() {}

    public function send(mixed $notifiable, mixed $notification): Collection
    {
        if (is_string($notification)) {
            return $notifiable->notify(new Notification($notification));
        }

        return $notifiable->notify($notification);
    }

    public function route(string $channel, mixed $notifiable): RouteNotificationMessage
    {
        return new RouteNotificationMessage($channel, $notifiable);
    }
}
