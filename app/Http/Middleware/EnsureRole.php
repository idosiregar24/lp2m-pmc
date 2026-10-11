<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route gate by users.role, e.g. ->middleware('role:ADMIN').
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $allowedRoles = array_map(UserRole::from(...), $roles);

        // Super Admin automatically passes checks that permit ADMIN
        if ($user->role === UserRole::SuperAdmin && in_array(UserRole::Admin, $allowedRoles, true)) {
            return $next($request);
        }

        abort_unless(in_array($user->role, $allowedRoles, true), 403);

        return $next($request);
    }
}
