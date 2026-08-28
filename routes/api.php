<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Synetro\Fuse\Http\Controllers\HealthController;

Route::prefix(config('fuse.routes.prefix', 'fuse'))
    ->middleware(config('fuse.routes.middleware', ['api']))
    ->group(function () {
        Route::get('/health/live', [HealthController::class, 'live']);
        Route::get('/health/ready', [HealthController::class, 'ready']);
    });
