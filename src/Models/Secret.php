<?php

declare(strict_types=1);

namespace Synetro\Fuse\Models;

use Illuminate\Database\Eloquent\Model;

class Secret extends Model
{
    protected $table = 'fuse_secrets';

    protected $fillable = [
        'key',
        'encrypted_value',
        'description',
        'version',
        'expires_at',
    ];
}
