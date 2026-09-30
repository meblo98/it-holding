<?php

namespace App\Http\Controllers;

use App\Models\ProfessionalProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerProfileController extends Controller
{
    /**
     * Profil professionnel unifié (doc §9), renseigné par le professionnel.
     * Le niveau de vérification, le statut fiscal et les badges restent
     * gérés par IT Holding.
     */
    public function edit()
    {
        $user = Auth::user();
        abort_unless($user->isPartner() || $user->isPartnerPending(), 403);

        $profile = $user->ensureProfessionalProfile();
        $badges = $user->validBadges()->get();

        return view('pages.shop.partner.profile', compact('user', 'profile', 'badges'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->isPartner() || $user->isPartnerPending(), 403);

        $profile = $user->ensureProfessionalProfile();
        $data = ProfessionalProfile::normalizeInput($request->validate(ProfessionalProfile::editableRules()));

        // Les champs de structure ne concernent que les prestataires.
        if ($user->partner_type !== 'prestataire') {
            unset($data['company_name'], $data['rccm'], $data['legal_representative'], $data['team_size'], $data['website']);
        }

        $identityChanged = collect(ProfessionalProfile::IDENTITY_FIELDS)
            ->filter(fn ($field) => array_key_exists($field, $data))
            ->contains(fn ($field) => (string) ($data[$field] ?? '') !== (string) ($profile->{$field} ?? ''));

        $message = 'Profil mis à jour.';
        if ($identityChanged && $profile->verification_level >= 3) {
            $data['verification_level'] = 2;
            $message .= ' Vos informations légales ont changé : IT Holding va les vérifier à nouveau.';
        }

        $profile->update($data);

        return redirect()->route('dashboard.partner.profile')->with('success', $message);
    }
}
