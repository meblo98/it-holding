<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RedirectAdminToAdminDashboard
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user && ($user->isAdmin() || $user->isStaff())) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
