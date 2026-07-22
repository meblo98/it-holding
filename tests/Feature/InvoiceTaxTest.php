<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTaxTest extends TestCase
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
            'name' => 'Produit Test Tax',
            'slug' => 'produit-test-tax',
            'price' => 10000,
            'purchase_price' => 7000,
            'stock' => 10,
            'active' => true,
            'condition' => 'new',
        ]);
    }

    /** @test */
    public function an_admin_can_create_invoice_with_tax()
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.invoices.store'), [
            'number' => 'FAC-2026-9999',
            'client_name' => 'Client Test Tax',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'description' => $this->product->name,
                    'quantity' => 2,
                    'unit_price' => 10000,
                ]
            ],
            'tax_amount' => 3600, // 18% of 20000
        ]);

        $response->assertRedirect();
        
        $invoice = Invoice::where('number', 'FAC-2026-9999')->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(20000, $invoice->subtotal);
        $this->assertEquals(3600, $invoice->tax_amount);
        $this->assertEquals(23600, $invoice->total_amount);
    }

    /** @test */
    public function an_admin_can_update_invoice_with_tax()
    {
        $invoice = Invoice::create([
            'number' => 'FAC-2026-8888',
            'client_name' => 'Client Test Tax',
            'subtotal' => 10000,
            'tax_amount' => 0,
            'total_amount' => 10000,
        ]);

        $response = $this->actingAs($this->adminUser)->put(route('admin.invoices.update', $invoice->id), [
            'number' => 'FAC-2026-8888',
            'client_name' => 'Client Test Tax Updated',
            'status' => 'draft',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'description' => $this->product->name,
                    'quantity' => 1,
                    'unit_price' => 10000,
                ]
            ],
            'tax_amount' => 1800, // 18% of 10000
        ]);

        $response->assertRedirect();
        
        $invoice->refresh();
        $this->assertEquals('Client Test Tax Updated', $invoice->client_name);
        $this->assertEquals(10000, $invoice->subtotal);
        $this->assertEquals(1800, $invoice->tax_amount);
        $this->assertEquals(11800, $invoice->total_amount);
    }

    /** @test */
    public function an_admin_can_create_quote_with_tax()
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.quotes.store'), [
            'number' => 'DEV-2026-9999',
            'client_name' => 'Client Test Tax',
            'items' => [
                [
                    'description' => 'Service Custom',
                    'quantity' => 1,
                    'unit_price' => 10000,
                ]
            ],
            'tax_amount' => 1800,
        ]);

        $response->assertRedirect();
        
        $quote = Quote::where('number', 'DEV-2026-9999')->first();
        $this->assertNotNull($quote);
        $this->assertEquals(10000, $quote->subtotal);
        $this->assertEquals(1800, $quote->tax_amount);
        $this->assertEquals(11800, $quote->total_amount);
    }
}
