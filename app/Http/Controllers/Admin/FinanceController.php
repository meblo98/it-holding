<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index()
    {
        $accounts = BankAccount::orderBy('name')->get();
        $transactions = BankTransaction::with(['bankAccount', 'invoice', 'client'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        // Get unpaid invoices for potential reconciliation options
        $unreconciledInvoices = Invoice::where('status', '!=', 'paid')
            ->where('type', 'invoice')
            ->orderBy('number')
            ->get();

        $clients = Client::orderBy('company_name')
            ->orderBy('last_name')
            ->get();

        return view('admin.finance.index', compact('accounts', 'transactions', 'unreconciledInvoices', 'clients'));
    }

    public function storeAccount(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'iban' => 'nullable|string|max:34',
            'rib' => 'nullable|string|max:30',
            'initial_balance' => 'required|numeric|min:0',
        ]);

        $validated['current_balance'] = $validated['initial_balance'];

        BankAccount::create($validated);

        return redirect()->route('admin.finance.index')->with('success', 'Compte bancaire créé avec succès.');
    }

    public function storeTransaction(Request $request)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0.01',
            'reference' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'transaction_date' => 'required|date',
            'invoice_id' => 'nullable|exists:invoices,id',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        DB::transaction(function () use ($validated) {
            $account = BankAccount::findOrFail($validated['bank_account_id']);
            
            // Adjust balance
            if ($validated['type'] === 'credit') {
                $account->increment('current_balance', $validated['amount']);
            } else {
                $account->decrement('current_balance', $validated['amount']);
            }

            // If an invoice is selected and this is credit, we can automatically mark the invoice as paid
            if ($validated['type'] === 'credit' && !empty($validated['invoice_id'])) {
                $invoice = Invoice::findOrFail($validated['invoice_id']);
                if ($invoice->status !== 'paid') {
                    $invoice->update(['status' => 'paid']);
                    
                    // Decrease client outstanding debt if linked
                    if ($invoice->client) {
                        $invoice->client->decrement('current_balance', $invoice->total_amount);
                    }
                }
                $validated['is_reconciled'] = true;
            }

            BankTransaction::create($validated);
        });

        return redirect()->route('admin.finance.index')->with('success', 'Transaction enregistrée avec succès.');
    }

    public function reconcile(Request $request, BankTransaction $transaction)
    {
        $validated = $request->validate([
            'invoice_id' => 'nullable|exists:invoices,id',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        DB::transaction(function () use ($transaction, $validated) {
            $transaction->update([
                'invoice_id' => $validated['invoice_id'] ?? null,
                'client_id' => $validated['client_id'] ?? null,
                'is_reconciled' => true,
            ]);

            // If reconciling a credit with an unpaid invoice, mark the invoice as paid
            if ($transaction->type === 'credit' && !empty($validated['invoice_id'])) {
                $invoice = Invoice::findOrFail($validated['invoice_id']);
                if ($invoice->status !== 'paid') {
                    $invoice->update(['status' => 'paid']);
                    if ($invoice->client) {
                        $invoice->client->decrement('current_balance', $invoice->total_amount);
                    }
                }
            }
        });

        return redirect()->route('admin.finance.index')->with('success', 'Rapprochement effectué avec succès.');
    }
}
