<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockAdjustmentTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user
        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);

        $this->product = Product::create([
            'name' => 'Produit Test Stock',
            'slug' => 'produit-test-stock',
            'price' => 10000,
            'purchase_price' => 7000,
            'stock' => 10,
            'active' => true,
            'condition' => 'new',
        ]);
    }

    /** @test */
    public function an_admin_can_adjust_stock_to_a_fixed_value()
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.stock.adjust', $this->product->id), [
            'type' => 'set',
            'quantity' => 15,
            'notes' => 'Ajustement inventaire'
        ]);

        $response->assertRedirect();
        
        $this->product->refresh();
        $this->assertEquals(15, $this->product->stock);

        // Check stock movement log
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'quantity' => 5, // 15 - 10
            'type' => 'adjustment',
            'notes' => 'Ajustement inventaire'
        ]);
    }

    /** @test */
    public function an_admin_can_add_to_stock()
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.stock.adjust', $this->product->id), [
            'type' => 'add',
            'quantity' => 5,
            'notes' => 'Ajout de stock'
        ]);

        $response->assertRedirect();
        
        $this->product->refresh();
        $this->assertEquals(15, $this->product->stock);

        // Check stock movement log
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'quantity' => 5,
            'type' => 'adjustment',
            'notes' => 'Ajout de stock'
        ]);
    }

    /** @test */
    public function an_admin_can_subtract_from_stock()
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.stock.adjust', $this->product->id), [
            'type' => 'add',
            'quantity' => -3,
            'notes' => 'Soustraction de stock'
        ]);

        $response->assertRedirect();
        
        $this->product->refresh();
        $this->assertEquals(7, $this->product->stock);

        // Check stock movement log
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'quantity' => -3,
            'type' => 'adjustment',
            'notes' => 'Soustraction de stock'
        ]);
    }

    /** @test */
    public function stock_cannot_be_negative()
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.stock.adjust', $this->product->id), [
            'type' => 'set',
            'quantity' => -5,
            'notes' => 'Stock negatif invalide'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        $this->product->refresh();
        $this->assertEquals(10, $this->product->stock); // unchanged
    }

    /** @test */
    public function it_allows_admin_to_view_stock_index_and_lists_products()
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.stock.index'));

        $response->assertStatus(200);
        $response->assertSee('Gestion de Stock');
        $response->assertSee('Produit Test Stock');
    }
}
