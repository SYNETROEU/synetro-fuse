<?php

declare(strict_types=1);

namespace Synetro\Fuse\Tests;

use Illuminate\Validation\ValidationException;
use Synetro\Fuse\Support\Facades\Fuse;

class ValidationTest extends TestCase
{
    public function test_validation_passes_with_valid_data(): void
    {
        $result = Fuse::validate(
            ['email' => 'test@example.com'],
            ['email' => 'required|email']
        );

        $this->assertTrue($result->passes());
    }

    public function test_validation_fails_with_invalid_data(): void
    {
        $result = Fuse::validate(
            ['email' => 'invalid'],
            ['email' => 'required|email']
        );

        $this->assertTrue($result->fails());
    }

    public function test_validation_throws_on_fail(): void
    {
        $this->expectException(ValidationException::class);

        Fuse::validate(
            ['email' => 'invalid'],
            ['email' => 'required|email']
        )->validate();
    }

    public function test_validation_returns_errors(): void
    {
        $result = Fuse::validate(
            ['email' => 'invalid'],
            ['email' => 'required|email']
        );

        $this->assertNotEmpty($result->errors()->all());
    }
}
