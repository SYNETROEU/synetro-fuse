<?php

declare(strict_types=1);

namespace Synetro\Fuse\Webhooks;

use Illuminate\Http\Client\Response;

class WebhookResponse
{
    public function __construct(
        protected Response $response,
    ) {}

    public function successful(): bool
    {
        return $this->response->successful();
    }

    public function status(): int
    {
        return $this->response->status();
    }

    public function body(): string
    {
        return $this->response->body();
    }
}
