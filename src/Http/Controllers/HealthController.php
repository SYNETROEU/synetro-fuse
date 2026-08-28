<?php

declare(strict_types=1);

namespace Synetro\Fuse\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Synetro\Fuse\Health\HealthManager;

class HealthController
{
    public function __construct(protected HealthManager $health) {}

    public function live(): JsonResponse
    {
        return response()->json(['status' => 'alive']);
    }

    public function ready(): JsonResponse
    {
        $results = $this->health->all();

        return response()->json([
            'status' => $this->health->status(),
            'checks' => $results,
        ]);
    }
}
