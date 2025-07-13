<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class Role
{
    public function handle(Request $request, Closure $next, $role, $guard = null)
    {
        $authGuard = \Auth::guard($guard);

        if ($authGuard->guest()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }
            return redirect()->guest('login');
        }

        $roles = is_array($role) ? $role : explode('|', $role);

        if (!$authGuard->user()->hasAnyRole($roles)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}