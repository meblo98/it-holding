<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role   = $request->input('role');

        $users = User::when($search, fn($q) => $q->where('name', 'like', "%$search%")->orWhere('email', 'like', "%$search%"))
                     ->when($role, fn($q) => $q->where('role', $role))
                     ->where('id', '!=', auth()->id())
                     ->orderBy('name')
                     ->paginate(20);

        return view('admin.users.index', compact('users', 'search', 'role'));
    }

    public function create()
    {
        $roles = User::ROLES;
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:' . implode(',', array_keys(User::ROLES)),
            'phone'    => 'nullable|string|max:50',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
            'phone'    => $validated['phone'] ?? null,
            'is_admin' => $validated['role'] === 'admin',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur {$user->name} créé avec le rôle « {$user->role_label} ».");
    }

    public function edit(User $user)
    {
        $roles = User::ROLES;
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:' . implode(',', array_keys(User::ROLES)),
            'phone'    => 'nullable|string|max:50',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role'     => $validated['role'],
            'phone'    => $validated['phone'] ?? null,
            'is_admin' => $validated['role'] === 'admin',
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur {$user->name} mis à jour.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé.');
    }
}
