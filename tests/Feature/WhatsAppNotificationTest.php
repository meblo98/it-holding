<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\Product;
use App\Models\Order;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class WhatsAppNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $client;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->client = Client::create([
            'user_id' => $this->user->id,
            'first_name' => 'Fatou',
            'last_name' => 'Ndiaye',
            'email' => 'fatou@example.com',
            'phone' => '772345678',
            'is_professional' => false,
            'wallet_balance' => 0,
        ]);

        $this->product = Product::create([
            'name' => 'Smartphone Samsung S24',
            'slug' => 'samsung-s24',
            'price' => 500000,
            'purchase_price' => 400000,
            'stock' => 10,
            'active' => true,
            'condition' => 'new',
        ]);
    }

    /** @test */
    public function it_logs_whatsapp_notification_when_disabled()
    {
        // Ensure WhatsApp is disabled in environment
        putenv('WHATSAPP_ENABLED=false');
        
        $order = Order::create([
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
            'customer_name' => 'Fatou Ndiaye',
            'customer_email' => 'fatou@example.com',
            'customer_phone' => '772345678',
            'customer_address' => 'Dakar, Senegal',
            'total_amount' => 500000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
        ]);

        $order->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => 500000,
            'purchase_price' => 400000,
        ]);

        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message) use ($order) {
                return str_contains($message, 'WhatsApp Notification (Simulation)') &&
                       str_contains($message, '#'.$order->id) &&
                       str_contains($message, 'Fatou Ndiaye');
            });

        WhatsAppService::notifyAdminForOrder($order);

        // Reset
        putenv('WHATSAPP_ENABLED');
    }

    /** @test */
    public function it_sends_http_request_when_whatsapp_enabled()
    {
        // Fake HTTP client
        Http::fake([
            'https://api.whatsapp-gateway.local/*' => Http::response(['status' => 'success'], 200),
        ]);

        // Temporarily put env parameters
        putenv('WHATSAPP_ENABLED=true');
        putenv('ADMIN_WHATSAPP_NUMBER=221772345678');
        putenv('WHATSAPP_API_URL=https://api.whatsapp-gateway.local/v1/send');
        putenv('WHATSAPP_TOKEN=test-token');

        $order = Order::create([
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
            'customer_name' => 'Fatou Ndiaye',
            'customer_email' => 'fatou@example.com',
            'customer_phone' => '772345678',
            'customer_address' => 'Dakar, Senegal',
            'total_amount' => 500000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
        ]);

        $order->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => 500000,
            'purchase_price' => 400000,
        ]);

        WhatsAppService::notifyAdminForOrder($order);

        Http::assertSent(function ($request) use ($order) {
            return $request->url() === 'https://api.whatsapp-gateway.local/v1/send' &&
                   $request['to'] === '221772345678' &&
                   $request['token'] === 'test-token' &&
                   str_contains($request['body'], '#'.$order->id);
        });

        // Reset env parameters
        putenv('WHATSAPP_ENABLED');
        putenv('ADMIN_WHATSAPP_NUMBER');
        putenv('WHATSAPP_API_URL');
        putenv('WHATSAPP_TOKEN');
    }
}
