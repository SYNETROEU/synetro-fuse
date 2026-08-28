<?php

declare(strict_types=1);

namespace Synetro\Fuse\Secrets;

class Secret
{
    public function __construct(
        protected string $key,
        protected SecretsManager $manager,
    ) {}

    public function get(): ?string
    {
        return $this->manager->get($this->key);
    }

    public function set(string $value): void
    {
        $this->manager->set($this->key, $value);
    }

    public function delete(): void
    {
        $this->manager->delete($this->key);
    }

    public function redact(): string
    {
        return $this->manager->redact($this->get() ?? '');
    }
}
