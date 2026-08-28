<?php

declare(strict_types=1);

namespace Synetro\Fuse\Testing;

class Flow
{
    protected $response;

    public function post(string $uri, array $data = []): self
    {
        $this->response = test()->postJson($uri, $data);

        return $this;
    }

    public function get(string $uri): self
    {
        $this->response = test()->getJson($uri);

        return $this;
    }

    public function put(string $uri, array $data = []): self
    {
        $this->response = test()->putJson($uri, $data);

        return $this;
    }

    public function delete(string $uri): self
    {
        $this->response = test()->deleteJson($uri);

        return $this;
    }

    public function assertCreated(): self
    {
        $this->response->assertStatus(201);

        return $this;
    }

    public function assertOk(): self
    {
        $this->response->assertStatus(200);

        return $this;
    }

    public function assertNoContent(): self
    {
        $this->response->assertNoContent();

        return $this;
    }

    public function assertNotFound(): self
    {
        $this->response->assertStatus(404);

        return $this;
    }

    public static function fake(): void
    {
        //
    }

    public static function assertActionRan(string $action): void
    {
        //
    }

    public static function assertWebhookSent(string $event): void
    {
        //
    }

    public static function assertNotificationSent(mixed $notifiable, string $notification): void
    {
        //
    }
}
