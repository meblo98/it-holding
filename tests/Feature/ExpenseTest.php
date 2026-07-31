<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $bankAccount;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);

        // Create a test bank account
        $this->bankAccount = BankAccount::create([
            'name' => 'SGBS Principal',
            'bank_name' => 'SGBS',
            'iban' => 'SN123456789012345678901234',
            'rib' => '1234567890',
            'initial_balance' => 500000.00,
            'current_balance' => 500000.00,
        ]);
    }

    /** @test */
    public function an_admin_can_view_the_expenses_list()
    {
        $expense = Expense::create([
            'title' => 'Achat café',
            'amount' => 15000,
            'category' => 'supplies',
            'payment_method' => 'cash',
            'expense_date' => now()->format('Y-m-d'),
            'user_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.expenses.index'));

        $response->assertStatus(200);
        $response->assertSee('Achat café');
        $response->assertSee('15 000');
    }

    /** @test */
    public function an_admin_can_create_an_expense_without_bank_account()
    {
        Storage::fake('public');

        $attachment = UploadedFile::fake()->image('facture.jpg');

        $response = $this->actingAs($this->admin)->post(route('admin.expenses.store'), [
            'title' => 'Fournitures de bureau',
            'amount' => 25000,
            'category' => 'supplies',
            'payment_method' => 'cash',
            'expense_date' => now()->format('Y-m-d'),
            'attachment' => $attachment,
            'description' => 'Achat stylos et cahiers',
        ]);

        $response->assertRedirect(route('admin.expenses.index'));

        $this->assertDatabaseHas('expenses', [
            'title' => 'Fournitures de bureau',
            'amount' => 25000,
            'category' => 'supplies',
            'payment_method' => 'cash',
            'bank_account_id' => null,
            'bank_transaction_id' => null,
        ]);

        $expense = Expense::first();
        $this->assertNotNull($expense->attachment);
        Storage::disk('public')->assertExists($expense->attachment);

        // Bank balance should remain unchanged
        $this->bankAccount->refresh();
        $this->assertEquals(500000.00, $this->bankAccount->current_balance);
    }

    /** @test */
    public function an_admin_can_create_an_expense_with_bank_account()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.expenses.store'), [
            'title' => 'Abonnement Internet',
            'amount' => 45000,
            'category' => 'telecom',
            'payment_method' => 'bank_transfer',
            'bank_account_id' => $this->bankAccount->id,
            'expense_date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('admin.expenses.index'));

        // Check bank balance decremented
        $this->bankAccount->refresh();
        $this->assertEquals(455000.00, $this->bankAccount->current_balance);

        // Check transaction was created
        $transaction = BankTransaction::latest()->first();
        $this->assertNotNull($transaction);
        $this->assertEquals('debit', $transaction->type);
        $this->assertEquals(45000.00, $transaction->amount);
        $this->assertEquals($this->bankAccount->id, $transaction->bank_account_id);

        // Check expense has the link to transaction
        $this->assertDatabaseHas('expenses', [
            'title' => 'Abonnement Internet',
            'amount' => 45000,
            'bank_account_id' => $this->bankAccount->id,
            'bank_transaction_id' => $transaction->id,
        ]);
    }

    /** @test */
    public function an_admin_can_update_an_expense_adjusting_bank_balances()
    {
        // 1. Create an expense of 50000 linked to SGBS
        $expense = Expense::create([
            'title' => 'Maintenance Clim',
            'amount' => 50000,
            'category' => 'other',
            'payment_method' => 'bank_transfer',
            'bank_account_id' => $this->bankAccount->id,
            'expense_date' => now()->format('Y-m-d'),
            'user_id' => $this->admin->id,
        ]);

        $transaction = BankTransaction::create([
            'bank_account_id' => $this->bankAccount->id,
            'type' => 'debit',
            'amount' => 50000,
            'reference' => 'EXP-00001',
            'description' => 'Dépense: Maintenance Clim',
            'transaction_date' => now()->format('Y-m-d'),
            'is_reconciled' => true,
        ]);
        $expense->update(['bank_transaction_id' => $transaction->id]);
        $this->bankAccount->decrement('current_balance', 50000); // balance becomes 450000

        // Create second bank account
        $cbao = BankAccount::create([
            'name' => 'CBAO Business',
            'bank_name' => 'CBAO',
            'initial_balance' => 300000.00,
            'current_balance' => 300000.00,
        ]);

        // Update expense: Change amount to 60000, and change bank account to CBAO
        $response = $this->actingAs($this->admin)->put(route('admin.expenses.update', $expense->id), [
            'title' => 'Maintenance Clim Modifiée',
            'amount' => 60000,
            'category' => 'other',
            'payment_method' => 'bank_transfer',
            'bank_account_id' => $cbao->id,
            'expense_date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('admin.expenses.index'));

        // SGBS balance should be restored to initial (450000 + 50000 = 500000)
        $this->bankAccount->refresh();
        $this->assertEquals(500000.00, $this->bankAccount->current_balance);

        // CBAO balance should be decremented by new amount (300000 - 60000 = 240000)
        $cbao->refresh();
        $this->assertEquals(240000.00, $cbao->current_balance);

        // Check transaction was updated
        $transaction->refresh();
        $this->assertEquals($cbao->id, $transaction->bank_account_id);
        $this->assertEquals(60000.00, $transaction->amount);
    }

    /** @test */
    public function an_admin_can_delete_an_expense_reverting_bank_balances()
    {
        $expense = Expense::create([
            'title' => 'Achat Imprimante',
            'amount' => 120000,
            'category' => 'supplies',
            'payment_method' => 'bank_transfer',
            'bank_account_id' => $this->bankAccount->id,
            'expense_date' => now()->format('Y-m-d'),
            'user_id' => $this->admin->id,
        ]);

        $transaction = BankTransaction::create([
            'bank_account_id' => $this->bankAccount->id,
            'type' => 'debit',
            'amount' => 120000,
            'reference' => 'EXP-00002',
            'description' => 'Dépense: Achat Imprimante',
            'transaction_date' => now()->format('Y-m-d'),
            'is_reconciled' => true,
        ]);
        $expense->update(['bank_transaction_id' => $transaction->id]);
        $this->bankAccount->decrement('current_balance', 120000); // 380000

        $response = $this->actingAs($this->admin)->delete(route('admin.expenses.destroy', $expense->id));

        $response->assertRedirect(route('admin.expenses.index'));

        // Check expense and transaction deleted
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
        $this->assertDatabaseMissing('bank_transactions', ['id' => $transaction->id]);

        // SGBS balance should be restored (380000 + 120000 = 500000)
        $this->bankAccount->refresh();
        $this->assertEquals(500000.00, $this->bankAccount->current_balance);
    }
}
