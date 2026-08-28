<?php

declare(strict_types=1);

namespace Synetro\Fuse\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FuseThrottleMiddleware
{
    public function handle(Request $request, Closure $next, ?string $maxAttempts = '60', ?string $decayMinutes = '1'): Response
    {
        $key = $request->ip();

        if (cache()->has("fuse.throttle.{$key}")) {
            return response()->json(['error' => 'Too Many Attempts'], 429);
        }

        cache()->put("fuse.throttle.{$key}", true, (int) $decayMinutes);

        return $next($request);
    }
}
