<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['bankAccount', 'user']);

        // Search filter
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('expense_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('expense_date', '<=', $request->input('end_date'));
        }

        // Calculate total of filtered expenses
        $totalAmount = (float) $query->sum('amount');

        // Paginate results
        $expenses = $query->orderBy('expense_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.expenses.index', compact('expenses', 'totalAmount'));
    }

    public function create()
    {
        $bankAccounts = BankAccount::orderBy('name')->get();
        return view('admin.expenses.create', compact('bankAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|string|in:' . implode(',', array_keys(Expense::CATEGORIES)),
            'payment_method' => 'required|string|in:' . implode(',', array_keys(Expense::PAYMENT_METHODS)),
            'bank_account_id' => 'nullable|required_if:payment_method,bank_transfer,check,card|exists:bank_accounts,id',
            'expense_date' => 'required|date',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:5120',
            'description' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('expenses', 'public');
            }

            // Create Expense
            $expense = Expense::create([
                'title' => $validated['title'],
                'amount' => $validated['amount'],
                'category' => $validated['category'],
                'payment_method' => $validated['payment_method'],
                'bank_account_id' => $validated['bank_account_id'] ?? null,
                'expense_date' => $validated['expense_date'],
                'attachment' => $attachmentPath,
                'description' => $validated['description'] ?? null,
                'user_id' => Auth::id(),
            ]);

            // Handle Bank Transaction and Account balance adjustment
            if (!empty($validated['bank_account_id'])) {
                $account = BankAccount::findOrFail($validated['bank_account_id']);
                $account->decrement('current_balance', $validated['amount']);

                $transaction = BankTransaction::create([
                    'bank_account_id' => $account->id,
                    'type' => 'debit',
                    'amount' => $validated['amount'],
                    'reference' => 'EXP-' . str_pad($expense->id, 5, '0', STR_PAD_LEFT),
                    'description' => "Dépense: " . $validated['title'] . (!empty($validated['description']) ? ' - ' . substr($validated['description'], 0, 50) : ''),
                    'transaction_date' => $validated['expense_date'],
                    'is_reconciled' => true,
                ]);

                // Link to expense
                $expense->update(['bank_transaction_id' => $transaction->id]);
            }
        });

        return redirect()->route('admin.expenses.index')->with('success', 'Dépense enregistrée avec succès.');
    }

    public function show(Expense $expense)
    {
        return view('admin.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $bankAccounts = BankAccount::orderBy('name')->get();
        return view('admin.expenses.edit', compact('expense', 'bankAccounts'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|string|in:' . implode(',', array_keys(Expense::CATEGORIES)),
            'payment_method' => 'required|string|in:' . implode(',', array_keys(Expense::PAYMENT_METHODS)),
            'bank_account_id' => 'nullable|required_if:payment_method,bank_transfer,check,card|exists:bank_accounts,id',
            'expense_date' => 'required|date',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:5120',
            'description' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($validated, $request, $expense) {
            $oldAmount = (float) $expense->amount;
            $oldBankAccountId = $expense->bank_account_id;
            $oldBankTransactionId = $expense->bank_transaction_id;

            // Handle attachment replacement
            $attachmentPath = $expense->attachment;
            if ($request->hasFile('attachment')) {
                if ($attachmentPath) {
                    Storage::disk('public')->delete($attachmentPath);
                }
                $attachmentPath = $request->file('attachment')->store('expenses', 'public');
            }

            // 1. Revert Old Bank Account Balance if there was an account associated
            if (!empty($oldBankAccountId) && !empty($oldBankTransactionId)) {
                $oldAccount = BankAccount::find($oldBankAccountId);
                if ($oldAccount) {
                    $oldAccount->increment('current_balance', $oldAmount);
                }
            }

            // Update local fields
            $expense->update([
                'title' => $validated['title'],
                'amount' => $validated['amount'],
                'category' => $validated['category'],
                'payment_method' => $validated['payment_method'],
                'bank_account_id' => $validated['bank_account_id'] ?? null,
                'expense_date' => $validated['expense_date'],
                'attachment' => $attachmentPath,
                'description' => $validated['description'] ?? null,
            ]);

            // 2. Process New/Updated Bank Account Balance
            if (!empty($validated['bank_account_id'])) {
                $newAccount = BankAccount::findOrFail($validated['bank_account_id']);
                $newAccount->decrement('current_balance', $validated['amount']);

                if ($oldBankTransactionId) {
                    // Update existing transaction
                    $transaction = BankTransaction::find($oldBankTransactionId);
                    if ($transaction) {
                        $transaction->update([
                            'bank_account_id' => $newAccount->id,
                            'amount' => $validated['amount'],
                            'description' => "Dépense: " . $validated['title'] . (!empty($validated['description']) ? ' - ' . substr($validated['description'], 0, 50) : ''),
                            'transaction_date' => $validated['expense_date'],
                        ]);
                    }
                } else {
                    // Create new transaction
                    $transaction = BankTransaction::create([
                        'bank_account_id' => $newAccount->id,
                        'type' => 'debit',
                        'amount' => $validated['amount'],
                        'reference' => 'EXP-' . str_pad($expense->id, 5, '0', STR_PAD_LEFT),
                        'description' => "Dépense: " . $validated['title'] . (!empty($validated['description']) ? ' - ' . substr($validated['description'], 0, 50) : ''),
                        'transaction_date' => $validated['expense_date'],
                        'is_reconciled' => true,
                    ]);
                    $expense->update(['bank_transaction_id' => $transaction->id]);
                }
            } else {
                // If there's no bank account but a transaction existed, delete the transaction
                if ($oldBankTransactionId) {
                    $transaction = BankTransaction::find($oldBankTransactionId);
                    if ($transaction) {
                        $transaction->delete();
                    }
                    $expense->update(['bank_transaction_id' => null]);
                }
            }
        });

        return redirect()->route('admin.expenses.index')->with('success', 'Dépense mise à jour avec succès.');
    }

    public function destroy(Expense $expense)
    {
        DB::transaction(function () use ($expense) {
            // Restore bank balance
            if (!empty($expense->bank_account_id) && !empty($expense->bank_transaction_id)) {
                $account = BankAccount::find($expense->bank_account_id);
                if ($account) {
                    $account->increment('current_balance', $expense->amount);
                }

                $transaction = BankTransaction::find($expense->bank_transaction_id);
                if ($transaction) {
                    $transaction->delete();
                }
            }

            // Delete attachment
            if ($expense->attachment) {
                Storage::disk('public')->delete($expense->attachment);
            }

            // Delete expense
            $expense->delete();
        });

        return redirect()->route('admin.expenses.index')->with('success', 'Dépense supprimée avec succès.');
    }
}
