<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warranty;
use App\Models\Client;
use App\Models\Product;
use App\Models\Invoice;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $type   = $request->input('type');

        $warranties = Warranty::with(['client', 'product'])
            ->when($search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('number', 'like', "%$search%")
                      ->orWhere('client_name', 'like', "%$search%")
                      ->orWhere('product_name', 'like', "%$search%")
                      ->orWhere('serial_number', 'like', "%$search%");
                });
            })
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($type,   fn($q) => $q->where('type',   $type))
            ->orderByDesc('expiry_date')
            ->paginate(20);

        // KPIs
        $stats = [
            'active'        => Warranty::active()->count(),
            'expired'       => Warranty::expired()->count(),
            'expiring_soon' => Warranty::expiringSoon(30)->count(),
            'total'         => Warranty::count(),
        ];

        return view('admin.warranties.index', compact('warranties', 'search', 'status', 'type', 'stats'));
    }

    public function create()
    {
        $clients  = Client::orderBy('first_name')->get();
        $products = Product::where('active', true)->orderBy('name')->get(['id', 'name']);
        $invoices = Invoice::latest()->limit(50)->get(['id', 'number', 'client_name']);

        return view('admin.warranties.create', compact('clients', 'products', 'invoices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'      => 'nullable|exists:clients,id',
            'product_id'     => 'nullable|exists:products,id',
            'invoice_id'     => 'nullable|exists:invoices,id',
            'product_name'   => 'required|string|max:255',
            'serial_number'  => 'nullable|string|max:255',
            'client_name'    => 'required|string|max:255',
            'client_phone'   => 'nullable|string|max:50',
            'purchase_date'  => 'required|date',
            'duration_months'=> 'required|integer|min:1|max:120',
            'type'           => 'required|in:standard,extended,care_plus',
            'status'         => 'required|in:active,expired,void,claimed',
            'coverage_notes' => 'nullable|string',
            'exclusions'     => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        // Auto-calculate expiry date
        $validated['expiry_date'] = \Carbon\Carbon::parse($validated['purchase_date'])
            ->addMonths($validated['duration_months'])
            ->format('Y-m-d');

        $validated['number'] = Warranty::generateNumber();

        $warranty = Warranty::create($validated);

        return redirect()->route('admin.warranties.show', $warranty->id)
            ->with('success', "Garantie {$warranty->number} créée avec succès.");
    }

    public function show(Warranty $warranty)
    {
        $warranty->load(['client', 'product', 'invoice', 'order']);
        return view('admin.warranties.show', compact('warranty'));
    }

    public function edit(Warranty $warranty)
    {
        $clients  = Client::orderBy('first_name')->get();
        $products = Product::where('active', true)->orderBy('name')->get(['id', 'name']);
        return view('admin.warranties.edit', compact('warranty', 'clients', 'products'));
    }

    public function update(Request $request, Warranty $warranty)
    {
        $validated = $request->validate([
            'client_id'      => 'nullable|exists:clients,id',
            'product_id'     => 'nullable|exists:products,id',
            'product_name'   => 'required|string|max:255',
            'serial_number'  => 'nullable|string|max:255',
            'client_name'    => 'required|string|max:255',
            'client_phone'   => 'nullable|string|max:50',
            'purchase_date'  => 'required|date',
            'duration_months'=> 'required|integer|min:1|max:120',
            'type'           => 'required|in:standard,extended,care_plus',
            'status'         => 'required|in:active,expired,void,claimed',
            'coverage_notes' => 'nullable|string',
            'exclusions'     => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        $validated['expiry_date'] = \Carbon\Carbon::parse($validated['purchase_date'])
            ->addMonths($validated['duration_months'])
            ->format('Y-m-d');

        $warranty->update($validated);

        return redirect()->route('admin.warranties.show', $warranty->id)
            ->with('success', 'Garantie mise à jour.');
    }

    public function destroy(Warranty $warranty)
    {
        $warranty->delete();
        return redirect()->route('admin.warranties.index')
            ->with('success', 'Garantie supprimée.');
    }
}
