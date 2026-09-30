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
        $redirectUrl = $this->sanitizeRedirect($request->query('redirect'));

        return redirect($redirectUrl);
    }

    /**
     * Only allow same-site relative paths. Rejects anything else (absolute URLs, and
     * protocol-relative paths like "//evil.com" or "/\evil.com" that browsers resolve as
     * off-site), which a naive "starts with http" check would miss, to prevent this public,
     * unauthenticated endpoint from being used as an open redirect for phishing.
     */
    private function sanitizeRedirect(?string $url): string
    {
        if (!is_string($url) || $url === '' || !str_starts_with($url, '/') || str_starts_with($url, '//') || str_starts_with($url, '/\\')) {
            return '/shop';
        }

        return $url;
    }
}
