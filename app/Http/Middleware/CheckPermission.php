<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     * Usage in routes: ->middleware('permission:services')
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!$request->user()->hasPermission($module)) {
            abort(403, "Accès non autorisé. Vous n'avez pas les droits requis pour accéder au module : " . $module);
        }

        return $next($request);
    }
}
