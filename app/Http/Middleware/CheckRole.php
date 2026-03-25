<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckRole Middleware
 *
 * Usage in routes: ->middleware('role:admin,gerente')
 *                  ->middleware('role:ingeniero')
 *                  ->middleware('permission:proyectos.ver')
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Not authenticated
        if (!$user) {
            return redirect()->route('signin');
        }

        // Account disabled
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('signin')->withErrors(['email' => 'Tu cuenta está desactivada. Contacta al administrador.']);
        }

        // Super admin bypasses everything
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        // Check if user has any of the required roles
        if (!empty($roles) && !$user->hasAnyRole($roles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para esta acción.'], 403);
            }
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
