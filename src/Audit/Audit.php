<?php

declare(strict_types=1);

namespace Synetro\Fuse\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Audit extends Model
{
    protected $table = 'fuse_audits';

    protected $fillable = [
        'action',
        'model',
        'model_id',
        'actor_type',
        'actor_id',
        'old_values',
        'new_values',
        'context',
        'request_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'context' => 'array',
    ];

    public function actor(): Relation
    {
        return $this->morphTo();
    }
}
