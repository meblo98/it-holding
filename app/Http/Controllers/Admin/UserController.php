<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RolePermission;
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

    public function permissions()
    {
        $roles = User::ROLES;
        // Exclude admin and client from list of role permissions to configure
        unset($roles['admin'], $roles['client']);

        $modules = [
            'services'       => 'Services',
            'projects'       => 'Portfolio',
            'posts'          => 'Blog',
            'products'       => 'Boutique',
            'orders'         => 'Commandes',
            'quotes'         => 'Devis',
            'invoices'       => 'Factures',
            'delivery-notes' => 'Bons de livraison',
            'suppliers'      => 'Fournisseurs',
            'stock'          => 'Gestion de stock',
            'clients'        => 'Clients CRM',
            'warranties'     => 'Garanties',
            'tickets'        => 'SAV & Tickets',
            'chat'           => 'Chat Support',
            'contracts'      => 'Contrats Maintenance',
            'care'           => 'IT HOLDING CARE+',
            'expenses'       => 'Gestion des Dépenses',
            'finance'        => 'Finance & Trésorerie',
            'reports'        => 'Rapports & Stats',
            'users'          => 'Équipe & Accès',
        ];

        // Fetch existing permissions
        $rolePermissions = RolePermission::pluck('permissions', 'role')->toArray();

        return view('admin.users.permissions', compact('roles', 'modules', 'rolePermissions'));
    }

    public function updatePermissions(Request $request)
    {
        $roles = User::ROLES;
        unset($roles['admin'], $roles['client']);

        $submittedPermissions = $request->input('permissions', []);

        foreach (array_keys($roles) as $role) {
            $rolePerms = $submittedPermissions[$role] ?? [];
            
            RolePermission::updateOrCreate(
                ['role' => $role],
                ['permissions' => $rolePerms]
            );
        }

        return redirect()->route('admin.users.permissions')
            ->with('success', 'Les accès pour chaque profil ont été mis à jour avec succès.');
    }

    public function approvePartner(User $user)
    {
        if ($user->role !== 'partner') {
            return back()->with('error', "Cet utilisateur n'est pas un partenaire.");
        }

        $user->update([
            'partner_status' => 'approved',
            'partner_code' => $user->partner_code ?: 'PART-' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
        ]);

        return back()->with('success', "Le partenaire {$user->name} a été approuvé avec succès. Code : {$user->partner_code}");
    }

    public function rejectPartner(User $user)
    {
        if ($user->role !== 'partner') {
            return back()->with('error', "Cet utilisateur n'est pas un partenaire.");
        }

        $user->update([
            'partner_status' => 'rejected',
        ]);

        return back()->with('success', "La candidature du partenaire {$user->name} a été rejetée.");
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
