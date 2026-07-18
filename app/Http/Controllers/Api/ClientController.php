<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user->isStaff()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $search = $request->input('search');
        $query = Client::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $clients = $query->latest()->paginate(15);

        $clients->getCollection()->transform(function ($client) {
            return $this->formatClient($client);
        });

        return response()->json($clients);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user->isStaff()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'rccm' => 'nullable|string|max:255',
            'ninea' => 'nullable|string|max:255',
            'sector' => 'nullable|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'required|string|max:50',
            'phone2' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'is_professional' => 'boolean',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['is_professional'] = $request->boolean('is_professional');
        $validated['current_balance'] = 0.00;
        $validated['wallet_balance'] = 0.00;

        $client = Client::create($validated);

        return response()->json([
            'message' => 'Client créé avec succès.',
            'client' => $this->formatClient($client)
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $client = Client::with(['warranties', 'orders', 'invoices', 'walletTransactions'])->findOrFail($id);

        // Security check: Clients can only see themselves
        if ($user->role === 'client') {
            $clientId = $user->client ? $user->client->id : null;
            if ($clientId !== $client->id) {
                return response()->json(['message' => 'Non autorisé.'], 403);
            }
        } elseif (!$user->isStaff()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        return response()->json([
            'client' => $this->formatClient($client, true)
        ]);
    }

    private function formatClient($client, $includeDetails = false)
    {
        $data = [
            'id' => $client->id,
            'first_name' => $client->first_name,
            'last_name' => $client->last_name,
            'full_name' => $client->full_name,
            'display_name' => $client->display_name,
            'company_name' => $client->company_name,
            'email' => $client->email,
            'phone' => $client->phone,
            'address' => $client->address,
            'city' => $client->city,
            'is_professional' => (bool)$client->is_professional,
            'wallet_balance' => $client->wallet_balance,
            'current_balance' => $client->current_balance,
            'credit_limit' => $client->credit_limit,
            'payment_terms' => $client->payment_terms,
        ];

        if ($includeDetails) {
            $data['warranties'] = $client->warranties->map(function ($w) {
                return [
                    'id' => $w->id,
                    'serial_number' => $w->serial_number,
                    'product_name' => $w->product_name ?? ($w->product ? $w->product->name : 'Produit inconnu'),
                    'status' => $w->status,
                    'expires_at' => $w->expires_at ? $w->expires_at->format('Y-m-d') : null,
                ];
            });

            $data['orders'] = $client->orders->take(10)->map(function ($o) {
                return [
                    'id' => $o->id,
                    'order_number' => $o->order_number,
                    'total' => $o->total_amount,
                    'status' => $o->status,
                    'created_at' => $o->created_at->format('Y-m-d H:i'),
                ];
            });

            $data['invoices'] = $client->invoices->take(10)->map(function ($inv) {
                return [
                    'id' => $inv->id,
                    'invoice_number' => $inv->invoice_number,
                    'total' => $inv->total_amount,
                    'status' => $inv->status,
                    'due_date' => $inv->due_date ? $inv->due_date->format('Y-m-d') : null,
                ];
            });

            $data['wallet_transactions'] = $client->walletTransactions->take(10)->map(function ($t) {
                return [
                    'id' => $t->id,
                    'type' => $t->type, // deposit, withdrawal, payment
                    'amount' => $t->amount,
                    'description' => $t->description,
                    'date' => $t->transaction_date ? $t->transaction_date->format('Y-m-d H:i') : null,
                ];
            });
        }

        return $data;
    }
}
