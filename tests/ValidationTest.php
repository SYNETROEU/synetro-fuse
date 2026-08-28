<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Synetro\Fuse\Tests\TestCase;

class ValidationTest extends TestCase
{
    public function test_validation_passes_with_valid_data(): void
    {
        $result = \Synetro\Fuse\Support\Facades\Fuse::validate(
            ['email' => 'test@example.com'],
            ['email' => 'required|email']
        );

        $this->assertTrue($result->passes());
    }

    public function test_validation_fails_with_invalid_data(): void
    {
        $result = \Synetro\Fuse\Support\Facades\Fuse::validate(
            ['email' => 'invalid'],
            ['email' => 'required|email']
        );

        $this->assertTrue($result->fails());
    }

    public function test_validation_throws_on_fail(): void
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        \Synetro\Fuse\Support\Facades\Fuse::validate(
            ['email' => 'invalid'],
            ['email' => 'required|email']
        )->validate();
    }

    public function test_validation_returns_errors(): void
    {
        $result = \Synetro\Fuse\Support\Facades\Fuse::validate(
            ['email' => 'invalid'],
            ['email' => 'required|email']
        );

        $this->assertNotEmpty($result->errors()->all());
    }
}
