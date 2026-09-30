<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class BictorysPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Define services config for testing
        config([
            'services.bictorys.key' => 'test_secret_key',
            'services.bictorys.base_url' => 'https://api.test.bictorys.com',
            'services.bictorys.webhook_secret' => 'test_webhook_secret',
        ]);
    }

    public function test_checkout_redirects_to_bictorys_upon_placing_order()
    {
        Http::fake([
            'https://api.test.bictorys.com/*' => Http::response([
                'redirectUrl' => 'https://checkout.bictorys.com/pay/session_123456'
            ], 200)
        ]);

        $user = User::factory()->create();
        $client = Client::create([
            'user_id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $user->email,
            'phone' => '771234567',
        ]);

        $product = Product::create([
            'name' => 'Produit Test',
            'slug' => 'produit-test',
            'price' => 15000,
            'stock' => 10,
            'active' => true,
        ]);

        // Put item in session cart
        $cart = [
            $product->id => [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => 2,
                'price' => 15000,
                'options' => [],
            ]
        ];

        $response = $this->actingAs($user)
            ->withSession(['cart' => $cart])
            ->post(route('shop.placeOrder'), [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => $user->email,
                'phone' => '771234567',
                'address' => 'Medina, Rue 6',
                'city' => 'Dakar',
                'country' => 'Sénégal',
                'zip' => '10000',
                'payment_method' => 'bictorys',
            ]);

        // Should redirect to Bictorys URL
        $response->assertRedirect('https://checkout.bictorys.com/pay/session_123456');

        // Check order was created in DB with correct payment_method and payment_status unpaid
        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals('bictorys', $order->payment_method);
        $this->assertEquals('unpaid', $order->payment_status);
        $this->assertEquals('pending', $order->status);
    }

    public function test_checkout_handles_bictorys_api_failure()
    {
        Http::fake([
            'https://api.test.bictorys.com/*' => Http::response([
                'error' => 'Invalid credentials'
            ], 401)
        ]);

        $user = User::factory()->create();
        $client = Client::create([
            'user_id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $user->email,
            'phone' => '771234567',
        ]);

        $product = Product::create([
            'name' => 'Produit Test',
            'slug' => 'produit-test',
            'price' => 15000,
            'stock' => 10,
            'active' => true,
        ]);

        $cart = [
            $product->id => [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => 2,
                'price' => 15000,
                'options' => [],
            ]
        ];

        $response = $this->actingAs($user)
            ->withSession(['cart' => $cart])
            ->from(route('shop.checkout'))
            ->post(route('shop.placeOrder'), [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => $user->email,
                'phone' => '771234567',
                'address' => 'Medina, Rue 6',
                'city' => 'Dakar',
                'country' => 'Sénégal',
                'zip' => '10000',
                'payment_method' => 'bictorys',
            ]);

        // Should redirect back to checkout with error
        $response->assertRedirect(route('shop.checkout'));
        $response->assertSessionHas('error');

        // The order itself is created and committed before the Bictorys API call is attempted (so the
        // stock decrement isn't held under a transaction for the duration of the external call); since
        // payment initiation failed, it is compensated: cancelled with its stock restored.
        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals('cancelled', $order->status);
        $this->assertEquals('failed', $order->payment_status);

        $product->refresh();
        $this->assertEquals(10, $product->stock);
    }

    public function test_bictorys_success_url_clears_cart()
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'customer_name' => 'John Doe',
            'customer_email' => $user->email,
            'customer_address' => 'Medina, Rue 6',
            'total_amount' => 30000,
            'payment_method' => 'bictorys',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['cart' => ['some_item'], 'promo_code' => 'test'])
            ->get(URL::signedRoute('shop.bictorys.success', ['order_id' => $order->id]));

        // Redirects to thanks page
        $response->assertRedirect(route('shop.thanks', $order->id));
        $response->assertSessionMissing('cart');
        $response->assertSessionMissing('promo_code');
    }

    public function test_bictorys_error_url_cancels_order_and_restores_stock()
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Produit Test',
            'slug' => 'produit-test',
            'price' => 7500,
            'stock' => 5,
            'active' => true,
        ]);
        
        $order = Order::create([
            'user_id' => $user->id,
            'customer_name' => 'John Doe',
            'customer_email' => $user->email,
            'customer_address' => 'Medina, Rue 6',
            'total_amount' => 15000,
            'payment_method' => 'bictorys',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        $orderItem = $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 7500,
        ]);

        $response = $this->actingAs($user)
            ->get(URL::signedRoute('shop.bictorys.error', ['order_id' => $order->id]));

        $response->assertRedirect(route('shop.checkout'));
        $response->assertSessionHas('error');

        $order->refresh();
        $this->assertEquals('failed', $order->payment_status);
        $this->assertEquals('cancelled', $order->status);

        // Stock should be restored (5 + 2 = 7)
        $product->refresh();
        $this->assertEquals(7, $product->stock);
    }

    public function test_bictorys_success_url_rejects_unsigned_request()
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'customer_name' => 'John Doe',
            'customer_email' => $user->email,
            'customer_address' => 'Medina, Rue 6',
            'total_amount' => 30000,
            'payment_method' => 'bictorys',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        // No signature: an attacker/visitor guessing the order id must not be able to hit this route.
        $response = $this->actingAs($user)
            ->get('/checkout/bictorys/success?order_id=' . $order->id);

        $response->assertStatus(403);
    }

    public function test_bictorys_error_url_rejects_unsigned_request_and_does_not_cancel_order()
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Produit Test',
            'slug' => 'produit-test',
            'price' => 7500,
            'stock' => 5,
            'active' => true,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'customer_name' => 'John Doe',
            'customer_email' => $user->email,
            'customer_address' => 'Medina, Rue 6',
            'total_amount' => 15000,
            'payment_method' => 'bictorys',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 7500,
        ]);

        // Another user tries to cancel/tamper with someone else's order via a guessed id, unsigned.
        $attacker = User::factory()->create();
        $response = $this->actingAs($attacker)
            ->get('/checkout/bictorys/error?order_id=' . $order->id);

        $response->assertStatus(403);

        $order->refresh();
        $this->assertEquals('unpaid', $order->payment_status);
        $this->assertEquals('pending', $order->status);

        $product->refresh();
        $this->assertEquals(5, $product->stock);
    }

    public function test_thanks_page_forbids_viewing_another_users_order()
    {
        $owner = User::factory()->create();
        $order = Order::create([
            'user_id' => $owner->id,
            'customer_name' => 'John Doe',
            'customer_email' => $owner->email,
            'customer_address' => 'Medina, Rue 6',
            'total_amount' => 15000,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        $stranger = User::factory()->create();
        $response = $this->actingAs($stranger)->get(route('shop.thanks', $order->id));

        $response->assertStatus(403);
    }

    public function test_bictorys_webhook_rejects_amount_mismatch()
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'customer_name' => 'John Doe',
            'customer_email' => $user->email,
            'customer_address' => 'Medina, Rue 6',
            'total_amount' => 30000,
            'payment_method' => 'bictorys',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        $response = $this->postJson(route('webhook.bictorys'), [
            'paymentReference' => (string) $order->id,
            'status' => 'succeeded',
            'amount' => 1, // Does not match the order's actual total
            'currency' => 'XOF',
        ], [
            'X-Secret-Key' => 'test_webhook_secret',
        ]);

        $response->assertStatus(422);

        $order->refresh();
        $this->assertEquals('unpaid', $order->payment_status);
    }

    public function test_bictorys_webhook_validates_secret_key()
    {
        $response = $this->postJson(route('webhook.bictorys'), [
            'paymentReference' => '123',
            'status' => 'succeeded'
        ], [
            'X-Secret-Key' => 'invalid_secret'
        ]);

        $response->assertStatus(401);
    }

    public function test_bictorys_webhook_marks_order_as_paid()
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'customer_name' => 'John Doe',
            'customer_email' => $user->email,
            'customer_address' => 'Medina, Rue 6',
            'total_amount' => 30000,
            'payment_method' => 'bictorys',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        $response = $this->postJson(route('webhook.bictorys'), [
            'paymentReference' => (string) $order->id,
            'status' => 'succeeded',
            'amount' => $order->total_amount,
            'currency' => 'XOF'
        ], [
            'X-Secret-Key' => 'test_webhook_secret'
        ]);

        $response->assertStatus(200);

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('processing', $order->status);
    }

    public function test_bictorys_webhook_marks_order_as_failed_on_failure_event()
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Produit Test',
            'slug' => 'produit-test',
            'price' => 5000,
            'stock' => 10,
            'active' => true,
        ]);
        
        $order = Order::create([
            'user_id' => $user->id,
            'customer_name' => 'John Doe',
            'customer_email' => $user->email,
            'customer_address' => 'Medina, Rue 6',
            'total_amount' => 15000,
            'payment_method' => 'bictorys',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        $orderItem = $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 3,
            'price' => 5000,
        ]);

        $response = $this->postJson(route('webhook.bictorys'), [
            'paymentReference' => (string) $order->id,
            'status' => 'failed',
            'amount' => $order->total_amount,
            'currency' => 'XOF'
        ], [
            'X-Secret-Key' => 'test_webhook_secret'
        ]);

        $response->assertStatus(200);

        $order->refresh();
        $this->assertEquals('failed', $order->payment_status);
        $this->assertEquals('cancelled', $order->status);

        // Stock should be restored
        $product->refresh();
        $this->assertEquals(13, $product->stock);
    }
}
