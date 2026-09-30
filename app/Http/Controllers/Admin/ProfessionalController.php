<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\ProfessionalProfile;
use App\Models\ProWalletEntry;
use App\Models\User;
use App\Models\UserBadge;
use App\Services\ProWallet;
use Illuminate\Http\Request;

class ProfessionalController extends Controller
{
    public function show(User $user)
    {
        abort_unless($user->role === 'partner', 404);

        // Profils approuvés avant la Phase 1 : on complète le profil manquant.
        $profile = $user->partner_status === 'approved'
            ? $user->getOrCreateProfessionalProfile()
            : $user->ensureProfessionalProfile();

        $userBadges = $user->userBadges()->with(['badge', 'issuer'])->latest('issued_at')->get();
        $badges = Badge::where('active', true)->orderBy('level')->get();

        $walletBalances = app(ProWallet::class)->balances($user);
        $walletEntries = ProWalletEntry::where('user_id', $user->id)->latest()->latest('id')->take(10)->get();

        return view('admin.professionals.show', compact('user', 'profile', 'userBadges', 'badges', 'walletBalances', 'walletEntries'));
    }

    /**
     * Niveau de vérification (doc §13) et informations pro / fiscales.
     */
    public function updateProfile(Request $request, User $user)
    {
        abort_unless($user->role === 'partner', 404);

        $validated = $request->validate([
            'verification_level' => 'required|integer|between:1,5',
            'beneficiary_type' => 'required|in:' . implode(',', array_keys(ProfessionalProfile::BENEFICIARY_TYPES)),
        ] + ProfessionalProfile::editableRules());

        $user->ensureProfessionalProfile()->update(ProfessionalProfile::normalizeInput($validated));

        return back()->with('success', 'Profil professionnel mis à jour.');
    }

    public function assignBadge(Request $request, User $user)
    {
        abort_unless($user->role === 'partner', 404);

        $validated = $request->validate([
            'badge_id' => 'required|exists:badges,id',
            'issued_at' => 'required|date',
            'expires_at' => 'nullable|date|after_or_equal:issued_at',
            'notes' => 'nullable|string|max:255',
        ]);

        $alreadyValid = $user->userBadges()->valid()->where('badge_id', $validated['badge_id'])->exists();
        if ($alreadyValid) {
            return back()->with('error', 'Ce professionnel possède déjà ce badge en cours de validité.');
        }

        $user->userBadges()->create($validated + [
            'number' => UserBadge::generateNumber(),
            'status' => 'active',
            'issued_by' => auth()->id(),
        ]);

        return back()->with('success', 'Badge délivré.');
    }

    public function revokeBadge(UserBadge $userBadge)
    {
        $userBadge->update(['status' => 'revoked']);

        return back()->with('success', "Badge {$userBadge->number} révoqué.");
    }
}
