<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceContract;
use App\Models\Client;
use Illuminate\Http\Request;

class MaintenanceContractController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $contracts = MaintenanceContract::with('client')
            ->when($search, fn($q, $s) => $q->where(fn($q) => $q
                ->where('number', 'like', "%$s%")
                ->orWhere('client_name', 'like', "%$s%")
            ))
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('end_date')
            ->paginate(20);

        $stats = [
            'active'        => MaintenanceContract::active()->count(),
            'expiring_soon' => MaintenanceContract::expiringSoon(30)->count(),
            'total'         => MaintenanceContract::count(),
            'revenue'       => MaintenanceContract::active()->sum('price'),
        ];

        return view('admin.contracts.index', compact('contracts', 'search', 'status', 'stats'));
    }

    public function create()
    {
        $clients = Client::orderBy('first_name')->get();
        return view('admin.contracts.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'               => 'nullable|exists:clients,id',
            'client_name'             => 'required|string|max:255',
            'client_phone'            => 'nullable|string|max:50',
            'client_address'          => 'nullable|string',
            'type'                    => 'required|in:basic,standard,premium,custom',
            'start_date'              => 'required|date',
            'end_date'                => 'required|date|after:start_date',
            'price'                   => 'required|numeric|min:0',
            'billing_period'          => 'required|in:monthly,quarterly,annual',
            'interventions_included'  => 'required|integer|min:0',
            'response_time_hours'     => 'required|integer|min:1',
            'scope'                   => 'nullable|string',
            'status'                  => 'required|in:draft,active,expired,cancelled,suspended',
            'payment_status'          => 'required|in:pending,partial,paid',
            'amount_paid'             => 'nullable|numeric|min:0',
            'notes'                   => 'nullable|string',
        ]);

        $validated['number']       = MaintenanceContract::generateNumber();
        $validated['amount_paid']  = $validated['amount_paid'] ?? 0;

        $contract = MaintenanceContract::create($validated);

        return redirect()->route('admin.contracts.show', $contract->id)
            ->with('success', "Contrat {$contract->number} créé avec succès.");
    }

    public function show(MaintenanceContract $contract)
    {
        $contract->load('client');
        return view('admin.contracts.show', compact('contract'));
    }

    public function edit(MaintenanceContract $contract)
    {
        $clients = Client::orderBy('first_name')->get();
        return view('admin.contracts.edit', compact('contract', 'clients'));
    }

    public function update(Request $request, MaintenanceContract $contract)
    {
        $validated = $request->validate([
            'client_id'               => 'nullable|exists:clients,id',
            'client_name'             => 'required|string|max:255',
            'client_phone'            => 'nullable|string|max:50',
            'client_address'          => 'nullable|string',
            'type'                    => 'required|in:basic,standard,premium,custom',
            'start_date'              => 'required|date',
            'end_date'                => 'required|date|after:start_date',
            'price'                   => 'required|numeric|min:0',
            'billing_period'          => 'required|in:monthly,quarterly,annual',
            'interventions_included'  => 'required|integer|min:0',
            'interventions_used'      => 'nullable|integer|min:0',
            'response_time_hours'     => 'required|integer|min:1',
            'scope'                   => 'nullable|string',
            'status'                  => 'required|in:draft,active,expired,cancelled,suspended',
            'payment_status'          => 'required|in:pending,partial,paid',
            'amount_paid'             => 'nullable|numeric|min:0',
            'notes'                   => 'nullable|string',
        ]);

        $validated['amount_paid']     = $validated['amount_paid'] ?? 0;
        $validated['interventions_used'] = $validated['interventions_used'] ?? 0;

        $contract->update($validated);

        return redirect()->route('admin.contracts.show', $contract->id)
            ->with('success', 'Contrat mis à jour.');
    }

    public function destroy(MaintenanceContract $contract)
    {
        $contract->delete();
        return redirect()->route('admin.contracts.index')
            ->with('success', 'Contrat supprimé.');
    }
}
