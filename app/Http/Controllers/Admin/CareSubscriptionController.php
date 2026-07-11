<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareSubscription;
use App\Models\Client;
use App\Models\Warranty;
use Illuminate\Http\Request;

class CareSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $plan   = $request->input('plan');

        $subscriptions = CareSubscription::with('client')
            ->when($search, fn($q, $s) => $q->where(fn($q) => $q
                ->where('number', 'like', "%$s%")
                ->orWhere('client_name', 'like', "%$s%")
                ->orWhere('product_name', 'like', "%$s%")
            ))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($plan,   fn($q) => $q->where('plan',   $plan))
            ->orderByDesc('end_date')
            ->paginate(20);

        $stats = [
            'active'        => CareSubscription::active()->count(),
            'expiring_soon' => CareSubscription::expiringSoon(30)->count(),
            'total'         => CareSubscription::count(),
            'revenue'       => CareSubscription::active()->sum('price'),
        ];

        return view('admin.care.index', compact('subscriptions', 'search', 'status', 'plan', 'stats'));
    }

    public function create()
    {
        $clients    = Client::orderBy('first_name')->get();
        $warranties = Warranty::active()->with('client')->latest()->get();
        return view('admin.care.create', compact('clients', 'warranties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'           => 'nullable|exists:clients,id',
            'warranty_id'         => 'nullable|exists:warranties,id',
            'client_name'         => 'required|string|max:255',
            'client_phone'        => 'nullable|string|max:50',
            'product_name'        => 'required|string|max:255',
            'serial_number'       => 'nullable|string|max:255',
            'plan'                => 'required|in:standard,premium,enterprise',
            'start_date'          => 'required|date',
            'duration_months'     => 'required|integer|min:1',
            'price'               => 'required|numeric|min:0',
            'has_priority_support'=> 'boolean',
            'has_repair_discount' => 'boolean',
            'repair_discount_pct' => 'nullable|integer|min:0|max:100',
            'has_parts_discount'  => 'boolean',
            'parts_discount_pct'  => 'nullable|integer|min:0|max:100',
            'has_home_service'    => 'boolean',
            'status'              => 'required|in:active,expired,cancelled,suspended',
            'payment_status'      => 'required|in:pending,paid',
            'notes'               => 'nullable|string',
        ]);

        $validated['number']              = CareSubscription::generateNumber();
        $validated['end_date']            = \Carbon\Carbon::parse($validated['start_date'])->addMonths($validated['duration_months'])->format('Y-m-d');
        $validated['has_priority_support'] = $request->boolean('has_priority_support');
        $validated['has_repair_discount']  = $request->boolean('has_repair_discount');
        $validated['has_parts_discount']   = $request->boolean('has_parts_discount');
        $validated['has_home_service']     = $request->boolean('has_home_service');
        $validated['amount_paid']          = $validated['payment_status'] === 'paid' ? $validated['price'] : 0;

        $sub = CareSubscription::create($validated);

        return redirect()->route('admin.care.show', $sub->id)
            ->with('success', "Abonnement CARE+ {$sub->number} créé.");
    }

    public function show(CareSubscription $care)
    {
        $care->load(['client', 'warranty']);
        return view('admin.care.show', compact('care'));
    }

    public function edit(CareSubscription $care)
    {
        $clients    = Client::orderBy('first_name')->get();
        $warranties = Warranty::active()->latest()->get();
        return view('admin.care.edit', compact('care', 'clients', 'warranties'));
    }

    public function update(Request $request, CareSubscription $care)
    {
        $validated = $request->validate([
            'client_id'           => 'nullable|exists:clients,id',
            'client_name'         => 'required|string|max:255',
            'client_phone'        => 'nullable|string|max:50',
            'product_name'        => 'required|string|max:255',
            'serial_number'       => 'nullable|string|max:255',
            'plan'                => 'required|in:standard,premium,enterprise',
            'start_date'          => 'required|date',
            'duration_months'     => 'required|integer|min:1',
            'price'               => 'required|numeric|min:0',
            'has_priority_support'=> 'boolean',
            'has_repair_discount' => 'boolean',
            'repair_discount_pct' => 'nullable|integer|min:0|max:100',
            'has_parts_discount'  => 'boolean',
            'parts_discount_pct'  => 'nullable|integer|min:0|max:100',
            'has_home_service'    => 'boolean',
            'status'              => 'required|in:active,expired,cancelled,suspended',
            'payment_status'      => 'required|in:pending,paid',
            'notes'               => 'nullable|string',
        ]);

        $validated['end_date']             = \Carbon\Carbon::parse($validated['start_date'])->addMonths($validated['duration_months'])->format('Y-m-d');
        $validated['has_priority_support'] = $request->boolean('has_priority_support');
        $validated['has_repair_discount']  = $request->boolean('has_repair_discount');
        $validated['has_parts_discount']   = $request->boolean('has_parts_discount');
        $validated['has_home_service']     = $request->boolean('has_home_service');

        $care->update($validated);

        return redirect()->route('admin.care.show', $care->id)
            ->with('success', 'Abonnement CARE+ mis à jour.');
    }

    public function destroy(CareSubscription $care)
    {
        $care->delete();
        return redirect()->route('admin.care.index')
            ->with('success', 'Abonnement supprimé.');
    }
}
