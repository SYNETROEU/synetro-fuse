<?php

declare(strict_types=1);

namespace Synetro\Fuse\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FuseMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!request_id()) {
            $request->headers->set('X-Fuse-Request-ID', (string) Str::ulid());
        }

        return $next($request);
    }
}
