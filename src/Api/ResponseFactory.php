<?php

declare(strict_types=1);

namespace Synetro\Fuse\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

class ResponseFactory
{
    public function __construct(
        protected mixed $data,
        protected int $status,
    ) {}

    public function respond(): JsonResponse
    {
        if ($this->data instanceof JsonResource) {
            return $this->data->response()->setStatusCode($this->status);
        }

        if ($this->data instanceof LengthAwarePaginator) {
            return response()->json([
                'data' => $this->data->items(),
                'meta' => [
                    'total' => $this->data->total(),
                    'per_page' => $this->data->perPage(),
                    'current_page' => $this->data->currentPage(),
                    'last_page' => $this->data->lastPage(),
                ],
            ], $this->status);
        }

        if (config('fuse.api.envelope', true)) {
            return response()->json(['data' => $this->data], $this->status);
        }

        return response()->json($this->data, $this->status);
    }
}
