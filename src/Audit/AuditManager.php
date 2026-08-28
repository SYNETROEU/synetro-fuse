<?php

declare(strict_types=1);

namespace Synetro\Fuse\Audit;

use Illuminate\Database\Connection;
use Illuminate\Events\Dispatcher;

class AuditManager
{
    public function __construct(
        protected Connection $db,
        protected Dispatcher $events,
    ) {}

    public function record(string $action, string $model, int $modelId, ?string $actorId = null, array $oldValues = [], array $newValues = [], array $context = []): Audit
    {
        $actor = auth()->user();

        $audit = Audit::create([
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'actor_type' => $actor ? $actor::class : null,
            'actor_id' => $actorId ?? $actor?->getKey(),
            'old_values' => $oldValues,
            'new_values' => $this->excludeSensitive($newValues),
            'context' => array_merge([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'request_id' => request_id(),
            ], $context),
        ]);

        $this->events->dispatch(new Events\AuditRecorded($audit));

        return $audit;
    }

    public function forModel(string $model, int $modelId): Collection
    {
        return Audit::where('model', $model)->where('model_id', $modelId)->orderByDesc('created_at')->get();
    }

    protected function excludeSensitive(array $values): array
    {
        $exclude = config('fuse.audit.exclude', []);

        foreach ($exclude as $field) {
            if (isset($values[$field])) {
                $values[$field] = '[REDACTED]';
            }
        }

        return $values;
    }
}
