<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\Warranty;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type   = $request->input('type'); // 'professional', 'individual'

        $clients = Client::withCount(['orders', 'invoices', 'warranties'])
            ->when($search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%$search%")
                      ->orWhere('last_name', 'like', "%$search%")
                      ->orWhere('company_name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ->orWhere('phone', 'like', "%$search%");
                });
            })
            ->when($type === 'professional', fn($q) => $q->where('is_professional', true))
            ->when($type === 'individual',   fn($q) => $q->where('is_professional', false))
            ->latest()
            ->paginate(20);

        // Stats
        $stats = [
            'total'        => Client::count(),
            'professional' => Client::where('is_professional', true)->count(),
            'individual'   => Client::where('is_professional', false)->count(),
            'with_credit'  => Client::where('current_balance', '>', 0)->count(),
        ];

        return view('admin.clients.index', compact('clients', 'search', 'type', 'stats'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'company_name'  => 'nullable|string|max:255',
            'rccm'          => 'nullable|string|max:100',
            'ninea'         => 'nullable|string|max:100',
            'sector'        => 'nullable|string|max:100',
            'email'         => 'nullable|email|unique:clients,email',
            'phone'         => 'nullable|string|max:50',
            'phone2'        => 'nullable|string|max:50',
            'address'       => 'nullable|string|max:500',
            'city'          => 'nullable|string|max:100',
            'country'       => 'nullable|string|max:100',
            'is_professional'  => 'boolean',
            'credit_limit'     => 'nullable|numeric|min:0',
            'payment_terms'    => 'nullable|in:semaine,15j,mois,trimestre',
            'notes'            => 'nullable|string',
        ]);

        $validated['is_professional'] = $request->boolean('is_professional');

        $client = Client::create($validated);

        return redirect()->route('admin.clients.show', $client->id)
            ->with('success', "Client {$client->display_name} créé avec succès.");
    }

    public function show(Client $client)
    {
        $client->load(['warranties', 'orders', 'invoices', 'quotes', 'walletTransactions']);
 
        $stats = [
            'total_orders'    => $client->orders->count(),
            'total_invoices'  => $client->invoices->count(),
            'total_revenue'   => $client->invoices->sum('total_amount'),
            'active_warranties' => $client->warranties->where('status', 'active')->count(),
            'balance'         => $client->current_balance,
            'wallet'          => $client->wallet_balance,
        ];
 
        $bankAccounts = \App\Models\BankAccount::orderBy('name')->get();

        return view('admin.clients.show', compact('client', 'stats', 'bankAccounts'));
    }

    public function deposit(Request $request, Client $client)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($client, $validated) {
            $client->increment('wallet_balance', $validated['amount']);
            
            $client->walletTransactions()->create([
                'type' => 'deposit',
                'amount' => $validated['amount'],
                'description' => $validated['description'] ?? 'Dépôt sur portefeuille',
                'transaction_date' => now(),
            ]);
        });

        return redirect()->route('admin.clients.show', $client->id)
            ->with('success', 'Dépôt effectué avec succès sur le portefeuille.');
    }

    public function payDebt(Request $request, Client $client)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($client, $validated) {
            $client->decrement('current_balance', $validated['amount']);

            if (!empty($validated['bank_account_id'])) {
                $bankAccount = \App\Models\BankAccount::findOrFail($validated['bank_account_id']);
                $bankAccount->increment('current_balance', $validated['amount']);

                \App\Models\BankTransaction::create([
                    'bank_account_id' => $bankAccount->id,
                    'type' => 'credit',
                    'amount' => $validated['amount'],
                    'reference' => 'REGLEMENT-CREANCE',
                    'description' => $validated['description'] ?? "Règlement créance client {$client->display_name}",
                    'transaction_date' => now(),
                    'client_id' => $client->id,
                    'is_reconciled' => true,
                ]);
            }
        });

        return redirect()->route('admin.clients.show', $client->id)
            ->with('success', 'Règlement de la créance enregistré avec succès.');
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'company_name'  => 'nullable|string|max:255',
            'rccm'          => 'nullable|string|max:100',
            'ninea'         => 'nullable|string|max:100',
            'sector'        => 'nullable|string|max:100',
            'email'         => 'nullable|email|unique:clients,email,' . $client->id,
            'phone'         => 'nullable|string|max:50',
            'phone2'        => 'nullable|string|max:50',
            'address'       => 'nullable|string|max:500',
            'city'          => 'nullable|string|max:100',
            'country'       => 'nullable|string|max:100',
            'is_professional'  => 'boolean',
            'credit_limit'     => 'nullable|numeric|min:0',
            'payment_terms'    => 'nullable|in:semaine,15j,mois,trimestre',
            'notes'            => 'nullable|string',
        ]);

        $validated['is_professional'] = $request->boolean('is_professional');

        $client->update($validated);

        return redirect()->route('admin.clients.show', $client->id)
            ->with('success', "Client {$client->display_name} mis à jour.");
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('admin.clients.index')
            ->with('success', 'Client supprimé.');
    }
}
