<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Synetro\Fuse\Security\SecurityManager;
use Synetro\Fuse\Security\SecurityResult;

class SecurityManagerTest extends TestCase
{
    public function test_headers_returns_security_headers(): void
    {
        $manager = app(SecurityManager::class);
        $headers = $manager->headers();

        $this->assertIsArray($headers);
    }

    public function test_check_returns_result(): void
    {
        $manager = app(SecurityManager::class);
        $result = $manager->check();

        $this->assertInstanceOf(SecurityResult::class, $result);
    }

    public function test_redact_masks_sensitive_values(): void
    {
        $manager = app(SecurityManager::class);
        $redacted = $manager->redact('sk_live_1234567890abcdef', 4);

        $this->assertStringContainsString('*', $redacted);
        $this->assertNotSame('sk_live_1234567890abcdef', $redacted);
    }

    public function test_token_generates_random_string(): void
    {
        $manager = app(SecurityManager::class);
        $token = $manager->token();

        $this->assertNotEmpty($token);
        $this->assertSame(32, strlen($token));
    }
}
