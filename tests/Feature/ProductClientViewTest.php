<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductClientViewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_the_product_warranty_duration_on_the_detail_page()
    {
        // Create product with 24 months warranty
        $product = Product::create([
            'name' => 'Écran Dell UltraSharp',
            'slug' => 'ecran-dell-ultrasharp',
            'description' => 'Un superbe écran Dell.',
            'price' => 180000,
            'stock' => 5,
            'active' => true,
            'warranty_duration_months' => 24,
        ]);

        $response = $this->get(route('shop.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee('Garantie:');
        $response->assertSee('24 mois (2 an(s))');
        $response->assertSee('Garantie 24 Mois');
    }

    /** @test */
    public function it_displays_default_text_when_product_has_no_warranty_duration()
    {
        // Create product with 0 warranty duration
        $product = Product::create([
            'name' => 'Souris Sans Fil Standard',
            'slug' => 'souris-sans-fil-standard',
            'description' => 'Souris basique.',
            'price' => 5000,
            'stock' => 10,
            'active' => true,
            'warranty_duration_months' => 0,
        ]);

        $response = $this->get(route('shop.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee('Garantie:');
        $response->assertSee('Sans garantie / Standard');
        $response->assertDontSee('Garantie null Mois');
    }
}
