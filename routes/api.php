<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix(config('fuse.routes.prefix', 'fuse'))
    ->middleware(config('fuse.routes.middleware', ['api']))
    ->group(function () {
        Route::get('/health/live', [\Synetro\Fuse\Http\Controllers\HealthController::class, 'live']);
        Route::get('/health/ready', [\Synetro\Fuse\Http\Controllers\HealthController::class, 'ready']);
    });
