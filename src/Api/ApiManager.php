<?php

declare(strict_types=1);

namespace Synetro\Fuse\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class ApiManager
{
    public function __construct() {}

    public function make(mixed $data = null, int $status = 200): ResponseFactory
    {
        return new ResponseFactory($data, $status);
    }

    public function created(mixed $data = null): JsonResponse
    {
        return $this->make($data, 201)->respond();
    }

    public function updated(mixed $data = null): JsonResponse
    {
        return $this->make($data, 200)->respond();
    }

    public function deleted(): JsonResponse
    {
        return response()->json(null, 204);
    }

    public function error(string $code, string $message, int $status = 400, array $meta = []): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'message' => $message,
                'meta' => $meta,
            ],
        ], $status);
    }
}
