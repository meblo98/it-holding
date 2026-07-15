<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\BankAccount;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class FinanceTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $client;
    protected $proClient;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);

        // Create a regular client
        $user = User::factory()->create();
        $this->client = Client::create([
            'user_id' => $user->id,
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean@example.com',
            'phone' => '771234567',
            'is_professional' => false,
            'wallet_balance' => 0,
            'current_balance' => 0,
        ]);

        // Create a professional client
        $proUser = User::factory()->create();
        $this->proClient = Client::create([
            'user_id' => $proUser->id,
            'first_name' => 'Entreprise',
            'last_name' => 'Tech',
            'company_name' => 'Tech Corp',
            'email' => 'tech@example.com',
            'phone' => '779876543',
            'is_professional' => true,
            'wallet_balance' => 0,
            'current_balance' => 0,
            'credit_limit' => 500000,
        ]);

        // Create a product
        $this->product = Product::create([
            'name' => 'Ordinateur Portable HP Pro',
            'slug' => 'hp-pro-1234',
            'price' => 200000,
            'purchase_price' => 150000,
            'stock' => 10,
            'active' => true,
            'condition' => 'new',
        ]);
    }

    /** @test */
    public function an_admin_can_view_finance_dashboard()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.finance.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function an_admin_can_create_a_bank_account()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.finance.bank-accounts.store'), [
            'name' => 'Société Générale SGBS',
            'bank_name' => 'SGBS',
            'account_number' => 'SN123456789012345678901',
            'initial_balance' => 1000000,
        ]);

        $response->assertRedirect(route('admin.finance.index'));
        $this->assertDatabaseHas('bank_accounts', [
            'name' => 'Société Générale SGBS',
            'current_balance' => 1000000,
        ]);
    }

    /** @test */
    public function a_client_can_have_money_deposited_into_their_wallet()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.clients.deposit', $this->client->id), [
            'amount' => 150000,
            'description' => 'Dépôt test',
        ]);

        $response->assertRedirect(route('admin.clients.show', $this->client->id));
        $this->client->refresh();
        $this->assertEquals(150000, $this->client->wallet_balance);
        $this->assertDatabaseHas('wallet_transactions', [
            'client_id' => $this->client->id,
            'type' => 'deposit',
            'amount' => 150000,
        ]);
    }

    /** @test */
    public function a_client_can_pay_outstanding_debt()
    {
        $account = BankAccount::create([
            'name' => 'Caisse Centrale',
            'bank_name' => 'SGBS',
            'account_number' => '12345',
            'current_balance' => 500000,
        ]);

        $this->client->update(['current_balance' => 100000]);

        $response = $this->actingAs($this->admin)->post(route('admin.clients.pay-debt', $this->client->id), [
            'amount' => 40000,
            'bank_account_id' => $account->id,
            'description' => 'Règlement partiel',
        ]);

        $response->assertRedirect(route('admin.clients.show', $this->client->id));
        
        $this->client->refresh();
        $account->refresh();

        $this->assertEquals(60000, $this->client->current_balance);
        $this->assertEquals(540000, $account->current_balance);

        $this->assertDatabaseHas('bank_transactions', [
            'bank_account_id' => $account->id,
            'type' => 'credit',
            'amount' => 40000,
            'client_id' => $this->client->id,
        ]);
    }

    /** @test */
    public function a_user_can_checkout_using_wallet_payment_method()
    {
        // Add product to session cart
        Session::put('cart', [
            $this->product->id => [
                'name' => $this->product->name,
                'quantity' => 1,
                'price' => $this->product->price,
                'image' => null,
                'slug' => $this->product->slug
            ]
        ]);

        // Give wallet money to client
        $this->client->update(['wallet_balance' => 300000]);

        $response = $this->actingAs($this->client->user)->post(route('shop.placeOrder'), [
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean@example.com',
            'phone' => '771234567',
            'address' => 'Medina, Rue 6',
            'city' => 'Dakar',
            'country' => 'Sénégal',
            'zip' => '10000',
            'payment_method' => 'wallet',
        ]);

        $this->client->refresh();
        // Product price is 200000. Balance should be 300000 - 200000 = 100000
        $this->assertEquals(100000, $this->client->wallet_balance);

        $order = Order::latest()->first();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('wallet', $order->payment_method);
        
        $this->assertDatabaseHas('wallet_transactions', [
            'client_id' => $this->client->id,
            'type' => 'payment',
            'amount' => 200000,
            'order_id' => $order->id,
        ]);
    }

    /** @test */
    public function a_user_cannot_checkout_using_wallet_if_insufficient_funds()
    {
        Session::put('cart', [
            $this->product->id => [
                'name' => $this->product->name,
                'quantity' => 1,
                'price' => $this->product->price,
                'image' => null,
                'slug' => $this->product->slug
            ]
        ]);

        // Give wallet money less than product price (200000)
        $this->client->update(['wallet_balance' => 150000]);

        $response = $this->actingAs($this->client->user)->post(route('shop.placeOrder'), [
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean@example.com',
            'phone' => '771234567',
            'address' => 'Medina, Rue 6',
            'city' => 'Dakar',
            'country' => 'Sénégal',
            'zip' => '10000',
            'payment_method' => 'wallet',
        ]);

        $response->assertSessionHas('error');
        $this->client->refresh();
        // Wallet balance should remain unchanged
        $this->assertEquals(150000, $this->client->wallet_balance);
        $this->assertEquals(0, Order::count());
    }

    /** @test */
    public function a_pro_user_can_checkout_using_credit_within_limits()
    {
        Session::put('cart', [
            $this->product->id => [
                'name' => $this->product->name,
                'quantity' => 2, // 2 * 200000 = 400000
                'price' => $this->product->price,
                'image' => null,
                'slug' => $this->product->slug
            ]
        ]);

        $response = $this->actingAs($this->proClient->user)->post(route('shop.placeOrder'), [
            'first_name' => 'Tech',
            'last_name' => 'Corp',
            'email' => 'tech@example.com',
            'phone' => '779876543',
            'address' => 'Plateau',
            'city' => 'Dakar',
            'country' => 'Sénégal',
            'zip' => '10000',
            'payment_method' => 'credit',
        ]);

        $this->proClient->refresh();
        // Debt balance should increase to 400000
        $this->assertEquals(400000, $this->proClient->current_balance);

        $order = Order::latest()->first();
        $this->assertEquals('unpaid', $order->payment_status);
        $this->assertEquals('credit', $order->payment_method);
    }

    /** @test */
    public function a_pro_user_cannot_checkout_using_credit_if_exceeding_limits()
    {
        Session::put('cart', [
            $this->product->id => [
                'name' => $this->product->name,
                'quantity' => 3, // 3 * 200000 = 600000 (Limit is 500000)
                'price' => $this->product->price,
                'image' => null,
                'slug' => $this->product->slug
            ]
        ]);

        $response = $this->actingAs($this->proClient->user)->post(route('shop.placeOrder'), [
            'first_name' => 'Tech',
            'last_name' => 'Corp',
            'email' => 'tech@example.com',
            'phone' => '779876543',
            'address' => 'Plateau',
            'city' => 'Dakar',
            'country' => 'Sénégal',
            'zip' => '10000',
            'payment_method' => 'credit',
        ]);

        $response->assertSessionHas('error');
        $this->proClient->refresh();
        $this->assertEquals(0, $this->proClient->current_balance);
        $this->assertEquals(0, Order::count());
    }

    /** @test */
    public function an_admin_can_view_invoice_receipt()
    {
        $invoice = \App\Models\Invoice::create([
            'number' => 'FAC-2026-9999',
            'client_id' => $this->client->id,
            'client_name' => 'Jean Dupont',
            'subtotal' => 200000,
            'total_amount' => 200000,
            'status' => 'sent',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.invoices.receipt', $invoice->id));
        $response->assertStatus(200);
        $response->assertSee('IT HOLDING SERVICES');
        $response->assertSee('FAC-2026-9999');
    }

    /** @test */
    public function an_admin_can_generate_credit_note_and_adjust_client_balance()
    {
        // Set client outstanding balance
        $this->client->update(['current_balance' => 300000]);

        $invoice = \App\Models\Invoice::create([
            'number' => 'FAC-2026-8888',
            'client_id' => $this->client->id,
            'client_name' => 'Jean Dupont',
            'subtotal' => 100000,
            'total_amount' => 100000,
            'status' => 'sent',
        ]);
        $invoice->items()->create([
            'description' => 'Service de Maintenance',
            'quantity' => 1,
            'unit_price' => 100000,
            'total_price' => 100000,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.invoices.credit-note', $invoice->id));
        
        $response->assertRedirect(route('admin.invoices.show', $invoice->id));
        
        $invoice->refresh();
        $this->client->refresh();

        // Original invoice should be cancelled
        $this->assertEquals('cancelled', $invoice->status);
        
        // Client current balance should be reduced: 300000 - 100000 = 200000
        $this->assertEquals(200000, $this->client->current_balance);

        // A new credit note should exist
        $this->assertDatabaseHas('invoices', [
            'type' => 'credit_note',
            'parent_invoice_id' => $invoice->id,
            'client_id' => $this->client->id,
            'total_amount' => 100000,
            'status' => 'paid',
        ]);
    }
}
