<?php

declare(strict_types=1);

namespace Synetro\Fuse\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'fuse_configs';

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
        'is_encrypted',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];
}
