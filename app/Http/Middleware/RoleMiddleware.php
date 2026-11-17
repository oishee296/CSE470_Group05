<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (! $request->user() || ! in_array($request->user()->role, $roles)) {
            abort(403, 'You are not allowed to access this page.');
        }

        return $next($request);
    }
}