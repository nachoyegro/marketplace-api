<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserRole;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        // Check if the user exists and has one of the required roles
        if (! $user || ! in_array($user->role->value, $roles)) {
            abort(403, 'Access denied: insufficient permissions.');
        }

        return $next($request);
    }
}
