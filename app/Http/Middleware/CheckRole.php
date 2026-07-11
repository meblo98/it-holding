<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Usage in routes: ->middleware('role:admin,dg')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // Admins always pass
        if ($user->is_admin || $user->role === 'admin') {
            return $next($request);
        }

        // Check if user's role is in the allowed list
        if (!empty($roles) && !in_array($user->role, $roles)) {
            abort(403, 'Accès non autorisé. Votre rôle ne vous permet pas d\'accéder à cette section.');
        }

        return $next($request);
    }
}
