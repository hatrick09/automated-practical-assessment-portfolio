<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Usage: ->middleware('role:admin') or ->middleware('role:admin,instructor')
     * 'hod' is a pseudo-role: role=instructor AND is_hod=true (see migration notes).
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'You are not authorized to access this page.');
        }

        $allowed = false;
        foreach ($roles as $role) {
            if ($role === 'hod' && $user->isHod()) {
                $allowed = true;
                break;
            }
            if ($user->role === $role) {
                $allowed = true;
                break;
            }
        }

        if (! $allowed) {
            abort(403, 'You are not authorized to access this page.');
        }

        return $next($request);
    }
}
