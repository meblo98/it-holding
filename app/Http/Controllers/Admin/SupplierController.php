<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('deliveryNotes')->latest()->paginate(10);
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:50',
            'address'        => 'nullable|string|max:1000',
            'contact_person' => 'nullable|string|max:255',
        ]);

        Supplier::create($validated);

        return redirect()->route('admin.suppliers.index')->with('success', 'Fournisseur créé avec succès.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['deliveryNotes' => function ($query) {
            $query->latest();
        }]);

        return view('admin.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:50',
            'address'        => 'nullable|string|max:1000',
            'contact_person' => 'nullable|string|max:255',
        ]);

        $supplier->update($validated);

        return redirect()->route('admin.suppliers.index')->with('success', 'Fournisseur mis à jour avec succès.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('admin.suppliers.index')->with('success', 'Fournisseur supprimé.');
    }
}
