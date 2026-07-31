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

    /** @test */
    public function it_filters_products_by_category()
    {
        // Dummy latest product to populate sidebar
        Product::create([
            'name' => 'Dummy Sidebar Featured',
            'slug' => 'dummy-sidebar-featured',
            'price' => 1000,
            'stock' => 5,
            'active' => true,
            'created_at' => now()->addMinutes(10),
        ]);

        $category = \App\Models\Category::create([
            'name' => 'Ordinateurs',
            'slug' => 'ordinateurs',
        ]);

        $product1 = Product::create([
            'name' => 'Ordinateur Dell Inspiron',
            'slug' => 'ordinateur-dell-inspiron',
            'price' => 300000,
            'stock' => 5,
            'active' => true,
            'category_id' => $category->id,
        ]);

        $product2 = Product::create([
            'name' => 'Clavier Basique',
            'slug' => 'clavier-basique',
            'price' => 15000,
            'stock' => 5,
            'active' => true,
        ]);

        // Get shop with category_id
        $response = $this->get(route('shop.index', ['category_id' => $category->id]));

        $response->assertStatus(200);
        $response->assertSee('Ordinateur Dell Inspiron');
        $response->assertDontSee('Clavier Basique');
    }

    /** @test */
    public function it_searches_products_using_q_and_search_parameters()
    {
        // Dummy latest product to populate sidebar
        Product::create([
            'name' => 'Dummy Sidebar Featured',
            'slug' => 'dummy-sidebar-featured',
            'price' => 1000,
            'stock' => 5,
            'active' => true,
            'created_at' => now()->addMinutes(10),
        ]);

        $product1 = Product::create([
            'name' => 'Super Portable Lenovo ThinkPad',
            'slug' => 'super-portable-lenovo-thinkpad',
            'price' => 450000,
            'stock' => 5,
            'active' => true,
        ]);

        $product2 = Product::create([
            'name' => 'Souris Gamer Razer',
            'slug' => 'souris-gamer-razer',
            'price' => 35000,
            'stock' => 5,
            'active' => true,
        ]);

        // Test search parameter
        $response = $this->get(route('shop.index', ['search' => 'Lenovo']));
        $response->assertStatus(200);
        $response->assertSee('Super Portable Lenovo ThinkPad');
        $response->assertDontSee('Souris Gamer Razer');

        // Test q parameter fallback
        $responseQ = $this->get(route('shop.index', ['q' => 'Razer']));
        $responseQ->assertStatus(200);
        $responseQ->assertSee('Souris Gamer Razer');
        $responseQ->assertDontSee('Super Portable Lenovo ThinkPad');
    }

    /** @test */
    public function it_displays_dynamic_promo_product_on_shop_sidebar()
    {
        $product = Product::create([
            'name' => 'Produit Promo Exclusif',
            'slug' => 'produit-promo-exclusif',
            'price' => 500000,
            'promo_price' => 450000,
            'stock' => 5,
            'active' => true,
        ]);

        $response = $this->get(route('shop.index'));

        $response->assertStatus(200);
        $response->assertSee('Produit Promo Exclusif');
        $response->assertSee('Offre Spéciale');
        $response->assertSee('450 000 CFA');
    }
}
