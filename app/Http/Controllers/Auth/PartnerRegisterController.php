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
        return view('auth.partner-register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'alpha_num', 'min:3', 'max:30', 'unique:users,username'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => strtolower($request->username),
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'partner',
            'partner_status' => 'pending',
        ]);

        // Generate temporary code based on ID
        $user->update([
            'partner_code' => 'PART-' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard.partner')->with('success', 'Votre demande d\'inscription partenaire a été soumise avec succès.');
    }
}
