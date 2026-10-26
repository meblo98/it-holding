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
            'latitude'          => 'nullable|numeric|between:-90,90',
            'longitude'         => 'nullable|numeric|between:-180,180',
            'service_radius_km' => 'nullable|integer|min:1|max:500',
            'rating'            => 'nullable|numeric|min:0|max:5',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['service_radius_km'] = $validated['service_radius_km'] ?? config('delivery.default_radius_km', 30);
        $validated['rating'] = $validated['rating'] ?? 3;

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
            'latitude'          => 'nullable|numeric|between:-90,90',
            'longitude'         => 'nullable|numeric|between:-180,180',
            'service_radius_km' => 'nullable|integer|min:1|max:500',
            'rating'            => 'nullable|numeric|min:0|max:5',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['service_radius_km'] = $validated['service_radius_km'] ?? config('delivery.default_radius_km', 30);
        $validated['rating'] = $validated['rating'] ?? 3;

        $supplier->update($validated);

        return redirect()->route('admin.suppliers.index')->with('success', 'Fournisseur mis à jour avec succès.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('admin.suppliers.index')->with('success', 'Fournisseur supprimé.');
    }
}
