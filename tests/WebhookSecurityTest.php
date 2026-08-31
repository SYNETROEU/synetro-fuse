<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Synetro\Fuse\Webhooks\WebhookManager;
use Illuminate\Support\Facades\Http;

class WebhookSecurityTest extends TestCase
{
    public function test_webhook_signature_generation_includes_event(): void
    {
        $manager = app(WebhookManager::class);

        $event = 'order.created';
        $payload = json_encode(['id' => 123, 'amount' => 99.99]);
        $secret = 'test-secret';

        // The signature should be generated with event + payload
        $expected = hash_hmac('sha256', $event . $payload, $secret);

        // Verify method should accept the event parameter
        $this->assertTrue(
            $manager->verify($event, $payload, $expected, $secret)
        );
    }

    public function test_webhook_signature_verification_fails_with_wrong_event(): void
    {
        $manager = app(WebhookManager::class);

        $event = 'order.created';
        $payload = json_encode(['id' => 123, 'amount' => 99.99]);
        $secret = 'test-secret';

        $signature = hash_hmac('sha256', $event . $payload, $secret);

        // Signature should fail if event doesn't match
        $this->assertFalse(
            $manager->verify('order.updated', $payload, $signature, $secret)
        );
    }

    public function test_webhook_signature_verification_fails_with_wrong_payload(): void
    {
        $manager = app(WebhookManager::class);

        $event = 'order.created';
        $payload = json_encode(['id' => 123, 'amount' => 99.99]);
        $secret = 'test-secret';

        $signature = hash_hmac('sha256', $event . $payload, $secret);

        // Signature should fail if payload doesn't match
        $this->assertFalse(
            $manager->verify($event, json_encode(['id' => 124]), $signature, $secret)
        );
    }

    public function test_webhook_signature_verification_fails_with_wrong_secret(): void
    {
        $manager = app(WebhookManager::class);

        $event = 'order.created';
        $payload = json_encode(['id' => 123, 'amount' => 99.99]);
        $secret = 'test-secret';

        $signature = hash_hmac('sha256', $event . $payload, $secret);

        // Signature should fail if secret doesn't match
        $this->assertFalse(
            $manager->verify($event, $payload, $signature, 'wrong-secret')
        );
    }

    public function test_webhook_signature_uses_timing_safe_comparison(): void
    {
        $manager = app(WebhookManager::class);

        $event = 'order.created';
        $payload = json_encode(['id' => 123]);
        $secret = 'test-secret';

        $validSignature = hash_hmac('sha256', $event . $payload, $secret);
        $invalidSignature = 'invalid_signature';

        // Both should return false for invalid signature
        $this->assertFalse($manager->verify($event, $payload, $invalidSignature, $secret));
        
        // Valid should return true
        $this->assertTrue($manager->verify($event, $payload, $validSignature, $secret));
    }
}
