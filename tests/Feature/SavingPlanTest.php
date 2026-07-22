<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\Product;
use App\Models\Service;
use App\Models\SavingPlan;
use App\Models\SavingTransaction;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\MaintenanceContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavingPlanTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $client;
    protected $product;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and a corresponding client
        $this->user = User::factory()->create();
        $this->client = Client::create([
            'user_id' => $this->user->id,
            'first_name' => 'Adama',
            'last_name' => 'Diop',
            'email' => 'adama@example.com',
            'phone' => '771234567',
            'is_professional' => false,
            'wallet_balance' => 500000,
        ]);

        // Create a product
        $this->product = Product::create([
            'name' => 'Ordinateur HP EliteBook',
            'slug' => 'hp-elitebook-840',
            'price' => 300000,
            'purchase_price' => 200000,
            'stock' => 10,
            'active' => true,
            'condition' => 'new',
        ]);

        // Create a service
        $this->service = Service::create([
            'title' => 'Installation Réseau',
            'slug' => 'installation-reseau',
            'description' => 'Installation et configuration de routeurs et switchs',
            'price' => 150000,
            'active' => true,
        ]);
    }

    /** @test */
    public function a_client_can_view_their_savings_plans()
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.savings'));

        $response->assertStatus(200);
        $response->assertSee("Mes Épargnes");
    }

    /** @test */
    public function a_client_can_view_creation_form_for_product_saving_plan()
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.savings.create', ['product_id' => $this->product->id]));

        $response->assertStatus(200);
        $response->assertSee($this->product->name);
        $response->assertSee("Dépôt Initial");
    }

    /** @test */
    public function a_client_can_view_creation_form_for_service_saving_plan()
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.savings.create', ['service_id' => $this->service->id]));

        $response->assertStatus(200);
        $response->assertSee($this->service->title);
        $response->assertSee("Dépôt Initial");
    }

    /** @test */
    public function a_client_can_initiate_a_savings_plan_with_zero_initial_deposit()
    {
        $response = $this->actingAs($this->user)->post(route('dashboard.savings.store'), [
            'product_id' => $this->product->id,
            'initial_deposit' => 0,
            'payment_method' => 'wallet',
        ]);

        $response->assertRedirect();
        
        $savingPlan = SavingPlan::where('client_id', $this->client->id)->first();
        $this->assertNotNull($savingPlan);
        $this->assertEquals($this->product->id, $savingPlan->product_id);
        $this->assertEquals(300000, $savingPlan->target_amount);
        $this->assertEquals(0, $savingPlan->current_amount);
        $this->assertEquals('active', $savingPlan->status);
    }

    /** @test */
    public function a_client_can_initiate_a_savings_plan_with_initial_deposit_from_wallet()
    {
        $response = $this->actingAs($this->user)->post(route('dashboard.savings.store'), [
            'product_id' => $this->product->id,
            'initial_deposit' => 100000,
            'payment_method' => 'wallet',
        ]);

        $response->assertRedirect();
        
        $savingPlan = SavingPlan::where('client_id', $this->client->id)->first();
        $this->assertNotNull($savingPlan);
        $this->assertEquals(100000, $savingPlan->current_amount);
        
        // Check wallet balance was deducted
        $this->client->refresh();
        $this->assertEquals(400000, $this->client->wallet_balance);

        // Check saving transaction was created
        $this->assertDatabaseHas('saving_transactions', [
            'saving_plan_id' => $savingPlan->id,
            'type' => 'deposit',
            'amount' => 100000,
            'payment_method' => 'wallet',
        ]);
    }

    /** @test */
    public function a_client_cannot_initiate_savings_plan_with_invalid_deposit_amount()
    {
        $response = $this->actingAs($this->user)->post(route('dashboard.savings.store'), [
            'product_id' => $this->product->id,
            'initial_deposit' => 400000, // higher than target amount 300000
            'payment_method' => 'wallet',
        ]);

        $response->assertSessionHasErrors(['initial_deposit']);
        $this->assertEquals(0, SavingPlan::count());
    }

    /** @test */
    public function a_client_cannot_initiate_savings_plan_with_insufficient_wallet_balance()
    {
        $this->client->update(['wallet_balance' => 50000]);

        $response = $this->actingAs($this->user)->post(route('dashboard.savings.store'), [
            'product_id' => $this->product->id,
            'initial_deposit' => 100000,
            'payment_method' => 'wallet',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(0, SavingPlan::count());
    }

    /** @test */
    public function a_client_can_make_deposits_to_an_active_saving_plan()
    {
        $plan = SavingPlan::create([
            'client_id' => $this->client->id,
            'product_id' => $this->product->id,
            'target_amount' => 300000,
            'current_amount' => 50000,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->post(route('dashboard.savings.deposit', $plan->id), [
            'amount' => 100000,
            'payment_method' => 'wallet',
        ]);

        $response->assertRedirect();
        
        $plan->refresh();
        $this->assertEquals(150000, $plan->current_amount);

        $this->client->refresh();
        $this->assertEquals(400000, $this->client->wallet_balance);
    }

    /** @test */
    public function reached_goal_automatically_finalizes_product_savings_plan_and_delivers_product()
    {
        $plan = SavingPlan::create([
            'client_id' => $this->client->id,
            'product_id' => $this->product->id,
            'target_amount' => 300000,
            'current_amount' => 200000,
            'status' => 'active',
        ]);

        // Deposit the remaining 100000 to reach 300000
        $response = $this->actingAs($this->user)->post(route('dashboard.savings.deposit', $plan->id), [
            'amount' => 100000,
            'payment_method' => 'wallet',
        ]);

        $response->assertRedirect();
        
        $plan->refresh();
        $this->assertEquals(300000, $plan->current_amount);
        $this->assertEquals('completed', $plan->status);

        // Check if Order was created
        $order = Order::where('client_id', $this->client->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('completed', $order->status);
        $this->assertEquals('paid', $order->payment_status);

        // Check if invoice was generated
        $invoice = Invoice::where('client_id', $this->client->id)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals('paid', $invoice->status);

        // Check stock was reduced from 10 to 9
        $this->product->refresh();
        $this->assertEquals(9, $this->product->stock);
    }

    /** @test */
    public function reached_goal_automatically_finalizes_service_savings_plan_and_creates_contract()
    {
        $plan = SavingPlan::create([
            'client_id' => $this->client->id,
            'service_id' => $this->service->id,
            'target_amount' => 150000,
            'current_amount' => 50000,
            'status' => 'active',
        ]);

        // Deposit remaining 100000
        $response = $this->actingAs($this->user)->post(route('dashboard.savings.deposit', $plan->id), [
            'amount' => 100000,
            'payment_method' => 'wallet',
        ]);

        $response->assertRedirect();
        
        $plan->refresh();
        $this->assertEquals(150000, $plan->current_amount);
        $this->assertEquals('completed', $plan->status);

        // Check if Maintenance Contract was created
        $contract = MaintenanceContract::where('client_id', $this->client->id)->first();
        $this->assertNotNull($contract);
        $this->assertEquals('active', $contract->status);

        // Check invoice
        $invoice = Invoice::where('client_id', $this->client->id)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals('paid', $invoice->status);
    }

    /** @test */
    public function a_client_can_withdraw_funds_early_and_cancel_active_plan()
    {
        $plan = SavingPlan::create([
            'client_id' => $this->client->id,
            'product_id' => $this->product->id,
            'target_amount' => 300000,
            'current_amount' => 150000,
            'status' => 'active',
        ]);

        // Client wallet before withdrawal was 500,000 F
        $response = $this->actingAs($this->user)->post(route('dashboard.savings.withdraw', $plan->id));

        $response->assertRedirect();

        $plan->refresh();
        $this->assertEquals('withdrawn', $plan->status);
        $this->assertEquals(0, $plan->current_amount);

        // Wallet balance should increase: 500,000 + 150,000 = 650,000 F
        $this->client->refresh();
        $this->assertEquals(650000, $this->client->wallet_balance);

        // Check withdrawal transaction
        $this->assertDatabaseHas('saving_transactions', [
            'saving_plan_id' => $plan->id,
            'type' => 'withdrawal',
            'amount' => -150000,
        ]);
    }
}
