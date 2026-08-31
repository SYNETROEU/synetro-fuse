<?php

declare(strict_types=1);

namespace Synetro\Fuse\Notifications;

class NotificationManager
{
    public function __construct() {}

    public function send(mixed $notifiable, mixed $notification): Collection
    {
        if (is_string($notification)) {
            return $notifiable->notify(new DatabaseNotification($notification));
        }

        return $notifiable->notify($notification);
    }

    public function route(string $channel, mixed $notifiable): RouteNotificationMessage
    {
        return new RouteNotificationMessage($channel, $notifiable);
    }
}
