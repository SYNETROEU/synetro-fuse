<?php

declare(strict_types=1);

namespace Synetro\Fuse\Models;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    protected $table = 'fuse_webhooks';

    protected $fillable = [
        'name',
        'url',
        'event',
        'payload',
        'status_code',
        'response',
        'attempt',
        'sent_at',
        'failed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
    ];
}
