<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\PackItem;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductPackTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $keyboard;
    protected $mouse;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@itholding.sn',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_admin' => true,
        ]);

        // Create components
        $this->keyboard = Product::create([
            'name' => 'Keyboard',
            'slug' => 'keyboard',
            'description' => 'A nice mechanical keyboard',
            'purchase_price' => 15000,
            'price' => 25000,
            'stock' => 10,
            'warranty_duration_months' => 12,
            'active' => true,
        ]);

        $this->mouse = Product::create([
            'name' => 'Mouse',
            'slug' => 'mouse',
            'description' => 'A wireless gaming mouse',
            'purchase_price' => 10000,
            'price' => 18000,
            'stock' => 8,
            'warranty_duration_months' => 12,
            'active' => true,
        ]);
    }

    /** @test */
    public function admin_can_create_product_pack()
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.products.store'), [
                'name' => 'Gamer Combo Pack',
                'description' => 'Keyboard and mouse bundle',
                'purchase_price' => 20000,
                'price' => 38000,
                'warranty_duration_months' => 12,
                'active' => 1,
                'is_pack' => 1,
                'pack_items' => [
                    ['product_id' => $this->keyboard->id, 'quantity' => 2],
                    ['product_id' => $this->mouse->id, 'quantity' => 1],
                ],
            ]);

        $response->assertRedirect(route('admin.products.index'));

        $pack = Product::where('name', 'Gamer Combo Pack')->first();
        $this->assertNotNull($pack);
        $this->assertTrue((bool)$pack->is_pack);

        // Verify relationship and quantites
        $this->assertCount(2, $pack->packItems);
        $this->assertDatabaseHas('pack_items', [
            'pack_id' => $pack->id,
            'product_id' => $this->keyboard->id,
            'quantity' => 2,
        ]);

        // Verify dynamic stock calculation: min(keyboard stock/2, mouse stock/1) = min(10/2, 8/1) = 5
        $this->assertEquals(5, $pack->stock);
    }

    /** @test */
    public function admin_can_update_product_pack()
    {
        $pack = Product::create([
            'name' => 'Old Combo Pack',
            'slug' => 'old-combo-pack',
            'description' => 'Old description',
            'purchase_price' => 20000,
            'price' => 35000,
            'warranty_duration_months' => 12,
            'active' => true,
            'is_pack' => true,
        ]);

        PackItem::create([
            'pack_id' => $pack->id,
            'product_id' => $this->keyboard->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.products.update', $pack->id), [
                'name' => 'Updated Combo Pack',
                'description' => 'Updated combo description',
                'purchase_price' => 22000,
                'price' => 40000,
                'warranty_duration_months' => 12,
                'active' => 1,
                'is_pack' => 1,
                'pack_items' => [
                    ['product_id' => $this->keyboard->id, 'quantity' => 3],
                    ['product_id' => $this->mouse->id, 'quantity' => 2],
                ],
            ]);

        $response->assertRedirect(route('admin.products.index'));

        $pack->refresh();
        $this->assertEquals('Updated Combo Pack', $pack->name);
        
        // Keyboard: stock 10 / 3 = 3.33 -> 3. Mouse: stock 8 / 2 = 4. Min = 3.
        $this->assertEquals(3, $pack->stock);
    }

    /** @test */
    public function pack_stock_decrements_components_on_checkout()
    {
        // 1. Create a Pack Product
        $pack = Product::create([
            'name' => 'Bundle Pack',
            'slug' => 'bundle-pack',
            'description' => 'Cool Bundle',
            'purchase_price' => 20000,
            'price' => 38000,
            'warranty_duration_months' => 12,
            'active' => true,
            'is_pack' => true,
        ]);

        PackItem::create([
            'pack_id' => $pack->id,
            'product_id' => $this->keyboard->id,
            'quantity' => 2,
        ]);

        PackItem::create([
            'pack_id' => $pack->id,
            'product_id' => $this->mouse->id,
            'quantity' => 1,
        ]);

        // 2. Add pack to session cart and post checkout
        $cart = [
            $pack->id => [
                'product_id' => $pack->id,
                'name' => $pack->name,
                'price' => $pack->price,
                'quantity' => 2,
            ]
        ];

        // Hit the shop checkout placeOrder route
        $response = $this->actingAs($this->adminUser)
            ->withSession(['cart' => $cart])
            ->post(route('shop.placeOrder'), [
                'first_name' => 'Jean',
                'last_name' => 'Pack',
                'email' => 'jean@pack.com',
                'phone' => '771234567',
                'address' => 'Dakar, SN',
                'city' => 'Dakar',
                'country' => 'Senegal',
                'zip' => '99000',
                'payment_method' => 'cod',
            ]);

        $response->assertStatus(302);

        // Components:
        // Keyboard: initial stock 10, bought 2 packs * 2 keyboards = 4. Expected remaining = 6.
        $this->keyboard->refresh();
        $this->assertEquals(6, $this->keyboard->stock);

        // Mouse: initial stock 8, bought 2 packs * 1 mouse = 2. Expected remaining = 6.
        $this->mouse->refresh();
        $this->assertEquals(6, $this->mouse->stock);

        // Pack stock: keyboard stock 6 / 2 = 3. mouse stock 6 / 1 = 6. Min = 3.
        $pack->refresh();
        $this->assertEquals(3, $pack->stock);
    }

    /** @test */
    public function manual_stock_adjustment_is_blocked_for_packs()
    {
        $pack = Product::create([
            'name' => 'Manual Blocked Pack',
            'slug' => 'manual-blocked-pack',
            'description' => 'Pack description',
            'purchase_price' => 20000,
            'price' => 38000,
            'warranty_duration_months' => 12,
            'active' => true,
            'is_pack' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.stock.adjust', $pack->id), [
                'type' => 'set',
                'quantity' => 15,
                'notes' => 'Ajustement inventaire'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Le stock des packs de produits est calculé de manière dynamique et ne peut pas être ajusté manuellement.');
    }

    /** @test */
    public function delivery_note_impacts_pack_components()
    {
        $pack = Product::create([
            'name' => 'Delivery Note Pack',
            'slug' => 'delivery-note-pack',
            'description' => 'Pack description',
            'purchase_price' => 20000,
            'price' => 38000,
            'warranty_duration_months' => 12,
            'active' => true,
            'is_pack' => true,
        ]);

        PackItem::create([
            'pack_id' => $pack->id,
            'product_id' => $this->keyboard->id,
            'quantity' => 2,
        ]);

        $supplier = \App\Models\Supplier::create([
            'name' => 'Supplier ABC',
            'email' => 'supplier@abc.com',
            'phone' => '770000000',
        ]);

        // Post delivery note (reception: received status)
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.delivery-notes.store'), [
                'number' => 'BL-REC-100',
                'type' => 'reception',
                'status' => 'received',
                'delivery_date' => now()->format('Y-m-d'),
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplier->name,
                'items' => [
                    [
                        'product_id' => $pack->id,
                        'quantity' => 3,
                        'purchase_price' => 20000,
                    ]
                ]
            ]);

        $response->assertRedirect();

        // Keyboard stock was 10. Received 3 packs, each has 2 keyboards = +6 keyboards. New stock = 16.
        $this->keyboard->refresh();
        $this->assertEquals(16, $this->keyboard->stock);

        // Pack stock should be 16 / 2 = 8
        $pack->refresh();
        $this->assertEquals(8, $pack->stock);
    }
}
