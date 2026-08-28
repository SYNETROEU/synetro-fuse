<?php

declare(strict_types=1);

namespace Synetro\Fuse\Webhooks;

class Webhook
{
    public function __construct(
        protected string $name,
        protected WebhookManager $manager,
    ) {}

    public function send(mixed $payload, array $options = []): WebhookResponse
    {
        $url = $this->resolveUrl();

        return $this->manager->send($url, $this->name, $payload, $options);
    }

    public function signed(): self
    {
        return $this;
    }

    public function queued(): self
    {
        return $this;
    }

    public function retry(int $times): self
    {
        return $this;
    }

    public function timeout(int $seconds): self
    {
        return $this;
    }

    protected function resolveUrl(): string
    {
        return config("fuse.webhooks.endpoints.{$this->name}", '');
    }
}
