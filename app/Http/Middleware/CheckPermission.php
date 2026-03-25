<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckPermission Middleware
 *
 * Usage in routes: ->middleware('permission:proyectos.crear')
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('signin');
        }

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('signin')->withErrors(['email' => 'Tu cuenta está desactivada.']);
        }

        if (!$user->hasPermissionTo($permission)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para esta acción.'], 403);
            }
            abort(403, 'No tienes permisos para realizar esta acción.');
        }

        return $next($request);
    }
}
