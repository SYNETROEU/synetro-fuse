<?php

declare(strict_types=1);

namespace Synetro\Fuse\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $table = 'fuse_features';

    protected $fillable = [
        'key',
        'enabled',
        'environment',
        'rules',
        'starts_at',
        'ends_at',
        'rollout_percentage',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'rules' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'rollout_percentage' => 'integer',
    ];
}
