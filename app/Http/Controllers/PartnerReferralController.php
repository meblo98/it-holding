<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class PartnerReferralController extends Controller
{
    public function track(Request $request, $identifier)
    {
        // Search by username or partner_code
        $partner = User::where('role', 'partner')
            ->where('partner_status', 'approved')
            ->where(function($query) use ($identifier) {
                $query->where('partner_code', $identifier)
                      ->orWhere('username', strtolower($identifier));
            })
            ->first();

        if ($partner) {
            // Set cookie for 30 days (43200 minutes)
            Cookie::queue('partner_ref', $partner->partner_code, 43200);
        }

        // Get redirect path or default to shop or home
        $redirectUrl = $request->query('redirect', '/shop');
        
        // Ensure redirect is safe (local path)
        if (str_starts_with($redirectUrl, 'http') && !str_contains($redirectUrl, $request->getHost())) {
            $redirectUrl = '/shop';
        }

        return redirect($redirectUrl);
    }
}
