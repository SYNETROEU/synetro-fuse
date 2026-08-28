<?php

declare(strict_types=1);

namespace Synetro\Fuse\Logging;

use Illuminate\Log\LogManager as IlluminateLogManager;

class LogManager
{
    public function __construct(
        protected IlluminateLogManager $log,
    ) {}

    public function event(string $name, array $context = []): self
    {
        return $this->context(array_merge(['event' => $name], $context));
    }

    public function user(mixed $user): self
    {
        return $this->context(['user' => $user]);
    }

    public function context(array $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function write(): void
    {
        $this->info($this->context['event'] ?? 'log', $this->context ?? []);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log->channel('single')->info($message, $this->enhanceContext($context));
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log->channel('single')->warning($message, $this->enhanceContext($context));
    }

    public function error(string $message, array $context = []): void
    {
        $this->log->channel('single')->error($message, $this->enhanceContext($context));
    }

    protected function enhanceContext(array $context): array
    {
        $context['request_id'] = request_id();
        $context['ip'] = request()->ip();
        $context['user_agent'] = request()->userAgent();

        return $context;
    }
}
