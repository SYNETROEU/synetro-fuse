<?php

declare(strict_types=1);

namespace Synetro\Fuse\Traits;

use Illuminate\Database\Eloquent\Model;
use Synetro\Fuse\Audit\AuditManager;
use Synetro\Fuse\Audit\Audit;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            app(AuditManager::class)->record('created', $model->getTable(), $model->getKey(), auth()->id(), [], $model->toArray());
        });

        static::updated(function (Model $model) {
            $changes = $model->getChanges();
            $original = $model->getOriginal();

            app(AuditManager::class)->record('updated', $model->getTable(), $model->getKey(), auth()->id(), $original, $changes);
        });

        static::deleted(function (Model $model) {
            app(AuditManager::class)->record('deleted', $model->getTable(), $model->getKey(), auth()->id(), $model->toArray(), []);
        });
    }

    public function audits()
    {
        return $this->morphMany(Audit::class, 'actor');
    }
}
