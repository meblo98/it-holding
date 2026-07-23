<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\Product;
use App\Models\Order;
use App\Models\PartnerPromoCode;
use App\Models\PartnerCommission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerPromoCodeTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $client;
    protected $partnerUser;
    protected $partnerClient;
    protected $product;
    protected $promoCode;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a customer user and a corresponding client
        $this->user = User::factory()->create();
        $this->client = Client::create([
            'user_id' => $this->user->id,
            'first_name' => 'Client',
            'last_name' => 'Demo',
            'email' => 'client@example.com',
            'phone' => '771112233',
            'is_professional' => false,
            'wallet_balance' => 200000,
        ]);

        // Create a partner user and a corresponding client
        $this->partnerUser = User::factory()->create();
        $this->partnerClient = Client::create([
            'user_id' => $this->partnerUser->id,
            'first_name' => 'Partner',
            'last_name' => 'Demo',
            'email' => 'partner@example.com',
            'phone' => '772223344',
            'is_professional' => false,
            'wallet_balance' => 0,
        ]);

        // Create a product
        $this->product = Product::create([
            'name' => 'Ordinateur HP EliteBook',
            'slug' => 'hp-elitebook-840',
            'price' => 100000,
            'purchase_price' => 70000,
            'stock' => 10,
            'active' => true,
            'condition' => 'new',
        ]);

        // Create a promo code for the partner
        $this->promoCode = PartnerPromoCode::create([
            'partner_id' => $this->partnerUser->id,
            'code' => 'PARTNERCODE',
            'discount_percent' => 5.00,
            'commission_percent' => 10.00,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_can_apply_a_valid_promo_code()
    {
        $response = $this->actingAs($this->user)->post(route('shop.promo.apply'), [
            'code' => 'PARTNERCODE',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('promo_code', 'PARTNERCODE');
        $response->assertSessionHas('success');
    }

    /** @test */
    public function it_fails_to_apply_an_invalid_promo_code()
    {
        $response = $this->actingAs($this->user)->post(route('shop.promo.apply'), [
            'code' => 'INVALIDCODE',
        ]);

        $response->assertRedirect();
        $response->assertSessionMissing('promo_code');
        $response->assertSessionHas('error');
    }

    /** @test */
    public function it_can_remove_an_applied_promo_code()
    {
        $response = $this->actingAs($this->user)
            ->withSession(['promo_code' => 'PARTNERCODE'])
            ->post(route('shop.promo.remove'));

        $response->assertRedirect();
        $response->assertSessionMissing('promo_code');
        $response->assertSessionHas('success');
    }

    /** @test */
    public function it_calculates_discount_on_checkout_page()
    {
        $response = $this->actingAs($this->user)
            ->withSession([
                'cart' => [
                    $this->product->id => [
                        'product_id' => $this->product->id,
                        'name' => $this->product->name,
                        'slug' => $this->product->slug,
                        'quantity' => 1,
                        'price' => 100000,
                        'options' => [],
                    ]
                ],
                'promo_code' => 'PARTNERCODE',
            ])
            ->get(route('shop.checkout'));

        $response->assertStatus(200);
        $response->assertViewHas('discount', 5000);
        $response->assertViewHas('discountedTotal', 95000);
    }

    /** @test */
    public function it_applies_discount_and_creates_pending_commission_on_order_placement()
    {
        $response = $this->actingAs($this->user)
            ->withSession([
                'cart' => [
                    $this->product->id => [
                        'product_id' => $this->product->id,
                        'name' => $this->product->name,
                        'slug' => $this->product->slug,
                        'quantity' => 1,
                        'price' => 100000,
                        'options' => [],
                    ]
                ],
                'promo_code' => 'PARTNERCODE',
            ])
            ->post(route('shop.placeOrder'), [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
                'phone' => '771234567',
                'address' => 'Medina',
                'city' => 'Dakar',
                'country' => 'Sénégal',
                'zip' => '10000',
                'payment_method' => 'cod',
            ]);

        $response->assertRedirect();

        // Check order was created with discount info
        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals($this->promoCode->id, $order->promo_code_id);
        $this->assertEquals(5000, $order->discount_amount);
        $this->assertEquals(95000, $order->total_amount); // 100,000 - 5,000 discount

        // Check partner commission record was created
        $commission = PartnerCommission::where('order_id', $order->id)->first();
        $this->assertNotNull($commission);
        $this->assertEquals($this->partnerUser->id, $commission->partner_id);
        $this->assertEquals(10000, $commission->commission_amount); // 10% of 100,000 order_amount
        $this->assertEquals('pending', $commission->status);

        // Check session was cleared
        $response->assertSessionMissing('cart');
        $response->assertSessionMissing('promo_code');
    }

    /** @test */
    public function it_pays_out_commission_when_order_is_completed()
    {
        // Place order first
        $order = Order::create([
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'total_amount' => 95000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
            'promo_code_id' => $this->promoCode->id,
            'discount_amount' => 5000,
        ]);

        $commission = PartnerCommission::create([
            'partner_id' => $this->partnerUser->id,
            'order_id' => $order->id,
            'promo_code_id' => $this->promoCode->id,
            'order_amount' => 100000,
            'commission_amount' => 10000,
            'status' => 'pending',
        ]);

        // Update status via admin OrderController update
        // We act as an admin user (admins might need role update if middleware exists)
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->put(route('orders.update', $order->id), [
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        $response->assertRedirect();

        // Check commission status is now paid
        $commission->refresh();
        $this->assertEquals('paid', $commission->status);

        // Check partner client wallet balance has been credited
        $this->partnerClient->refresh();
        $this->assertEquals(10000, $this->partnerClient->wallet_balance);

        // Check that a WalletTransaction of type deposit was logged for the partner client
        $this->assertDatabaseHas('wallet_transactions', [
            'client_id' => $this->partnerClient->id,
            'type' => 'deposit',
            'amount' => 10000,
            'order_id' => $order->id,
        ]);
    }

    /** @test */
    public function it_cancels_commission_when_order_is_cancelled()
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'total_amount' => 95000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
            'promo_code_id' => $this->promoCode->id,
            'discount_amount' => 5000,
        ]);

        $commission = PartnerCommission::create([
            'partner_id' => $this->partnerUser->id,
            'order_id' => $order->id,
            'promo_code_id' => $this->promoCode->id,
            'order_amount' => 100000,
            'commission_amount' => 10000,
            'status' => 'pending',
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->put(route('orders.update', $order->id), [
            'status' => 'cancelled',
            'payment_status' => 'unpaid',
        ]);

        $response->assertRedirect();

        // Check commission status is cancelled
        $commission->refresh();
        $this->assertEquals('cancelled', $commission->status);

        // Partner wallet balance should remain 0
        $this->partnerClient->refresh();
        $this->assertEquals(0, $this->partnerClient->wallet_balance);
    }

    /** @test */
    public function it_applies_tva_on_order_placement_when_enabled()
    {
        $response = $this->actingAs($this->user)
            ->withSession([
                'cart' => [
                    $this->product->id => [
                        'product_id' => $this->product->id,
                        'name' => $this->product->name,
                        'slug' => $this->product->slug,
                        'quantity' => 1,
                        'price' => 100000,
                        'options' => [],
                    ]
                ],
                'promo_code' => 'PARTNERCODE',
                'apply_tva' => true,
            ])
            ->post(route('shop.placeOrder'), [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
                'phone' => '771234567',
                'address' => 'Medina',
                'city' => 'Dakar',
                'country' => 'Sénégal',
                'zip' => '10000',
                'payment_method' => 'cod',
            ]);

        $response->assertRedirect();

        // Check order was created with discount & tax info
        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals($this->promoCode->id, $order->promo_code_id);
        $this->assertEquals(5000, $order->discount_amount);
        $this->assertEquals(17100, $order->tax_amount); // 18% of 95000
        $this->assertEquals(112100, $order->total_amount); // 95,000 + 17,100 tax
    }
}
