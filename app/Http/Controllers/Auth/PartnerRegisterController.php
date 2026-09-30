<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class PartnerRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $partnerTypes = User::PARTNER_TYPES;

        return view('auth.partner-register', compact('partnerTypes'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            // « register » est réservé : /partner/register est l'URL du formulaire d'inscription.
            'username' => ['required', 'string', 'alpha_num', 'min:3', 'max:30', 'unique:users,username', 'not_in:register'],
            'phone' => ['required', 'string', 'max:20'],
            'partner_type' => ['required', 'in:' . implode(',', array_keys(User::PARTNER_TYPES))],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'provider_kind' => ['exclude_unless:partner_type,prestataire', 'nullable', 'in:individual,structure'],
            'company_name' => ['exclude_unless:partner_type,prestataire', 'nullable', 'required_if:provider_kind,structure', 'string', 'max:255'],
            'ninea' => ['nullable', 'string', 'max:50'],
            'rccm' => ['nullable', 'string', 'max:100'],
        ], [
            'company_name.required_if' => 'La raison sociale est obligatoire pour une structure.',
            'username.not_in' => 'Cet identifiant est réservé, choisissez-en un autre.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => strtolower($request->username),
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'partner',
            'partner_status' => 'pending',
            'partner_type' => $request->partner_type,
        ]);

        // Generate temporary code based on ID
        $user->update([
            'partner_code' => 'PART-' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
        ]);

        // Prestataire structure (doc §7) : identité déclarée, à vérifier par IT Holding.
        // Pas d'ID pro public avant l'approbation.
        if ($request->partner_type === 'prestataire' && $request->provider_kind === 'structure') {
            $user->ensureProfessionalProfile()->update([
                'company_name' => $request->company_name,
                'ninea' => $request->ninea,
                'rccm' => $request->rccm,
                'beneficiary_type' => 'company',
            ]);
        }

        Auth::login($user);

        return redirect()->route('dashboard.partner')->with('success', 'Votre demande d\'inscription partenaire a été soumise avec succès.');
    }
}
