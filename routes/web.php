<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/fuse', function () {
    return response()->json(['name' => config('fuse.name', 'Fuse')]);
});
