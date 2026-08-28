<?php

declare(strict_types=1);

namespace Synetro\Fuse\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ResourceResponse
{
    public function index(LengthAwarePaginator $paginator): JsonResponse
    {
        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function show(Model $model): JsonResponse
    {
        return response()->json([
            'data' => $model,
        ]);
    }

    public function created(Model $model): JsonResponse
    {
        return response()->json([
            'data' => $model,
        ], 201);
    }

    public function updated(Model $model): JsonResponse
    {
        return response()->json([
            'data' => $model,
        ]);
    }

    public function deleted(): JsonResponse
    {
        return response()->json(null, 204);
    }
}
