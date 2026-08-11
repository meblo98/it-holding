<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class TrackPartnerReferral
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('ref') || $request->has('partner')) {
            $identifier = $request->query('ref') ?: $request->query('partner');
            
            $partner = User::where('role', 'partner')
                ->where('partner_status', 'approved')
                ->where(function($query) use ($identifier) {
                    $query->where('partner_code', $identifier)
                          ->orWhere('username', strtolower($identifier));
                })
                ->first();

            if ($partner) {
                // Set cookie for 30 days
                Cookie::queue('partner_ref', $partner->partner_code, 43200);
            }
        }

        return $next($request);
    }
}
