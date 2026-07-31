<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductSpecsTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

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
    }

    /** @test */
    public function admin_can_create_product_with_specs_and_fiche_technique()
    {
        Storage::fake('public');

        $ficheFile = UploadedFile::fake()->create('specs.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.products.store'), [
                'name' => 'Laptop Gamer Asus',
                'description' => 'Super laptop.',
                'purchase_price' => 500000,
                'price' => 700000,
                'stock' => 10,
                'warranty_duration_months' => 12,
                'active' => 1,
                'specs' => [
                    ['key' => 'Couleur', 'value' => 'Rouge'],
                    ['key' => 'RAM', 'value' => '16Go'],
                ],
                'fiche_technique' => $ficheFile,
            ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('name', 'Laptop Gamer Asus')->first();
        $this->assertNotNull($product);

        // Verify specs stored as associative array
        $this->assertEquals([
            'Couleur' => 'Rouge',
            'RAM' => '16Go',
        ], $product->specs);

        // Verify fiche_technique file stored
        $this->assertNotNull($product->fiche_technique);
        Storage::disk('public')->assertExists($product->fiche_technique);
    }

    /** @test */
    public function admin_can_update_product_specs_and_fiche_technique()
    {
        Storage::fake('public');

        $product = Product::create([
            'name' => 'Laptop Gamer Asus',
            'slug' => 'laptop-gamer-asus',
            'description' => 'Super laptop.',
            'purchase_price' => 500000,
            'price' => 700000,
            'stock' => 10,
            'warranty_duration_months' => 12,
            'active' => true,
            'specs' => ['Couleur' => 'Rouge'],
            'fiche_technique' => 'old_fiche.pdf',
        ]);

        $ficheFile = UploadedFile::fake()->create('new_specs.pdf', 150, 'application/pdf');

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.products.update', $product->id), [
                'name' => 'Laptop Gamer Asus',
                'description' => 'Updated desc.',
                'purchase_price' => 500000,
                'price' => 700000,
                'stock' => 10,
                'warranty_duration_months' => 12,
                'active' => 1,
                'specs' => [
                    ['key' => 'Couleur', 'value' => 'Bleu'],
                    ['key' => 'Stockage', 'value' => '1To SSD'],
                ],
                'fiche_technique' => $ficheFile,
            ]);

        $response->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertEquals([
            'Couleur' => 'Bleu',
            'Stockage' => '1To SSD',
        ], $product->specs);

        $this->assertNotEquals('old_fiche.pdf', $product->fiche_technique);
        Storage::disk('public')->assertExists($product->fiche_technique);
    }

    /** @test */
    public function client_can_see_specs_and_fiche_technique_on_product_page()
    {
        $product = Product::create([
            'name' => 'Laptop Gamer Asus',
            'slug' => 'laptop-gamer-asus',
            'description' => 'Super laptop.',
            'purchase_price' => 500000,
            'price' => 700000,
            'stock' => 10,
            'warranty_duration_months' => 12,
            'active' => true,
            'specs' => [
                'RAM' => '16Go',
                'Graphique' => 'RTX 4060',
            ],
            'fiche_technique' => 'products/tech_sheets/test.pdf',
        ]);

        $response = $this->get(route('shop.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee('RAM');
        $response->assertSee('16Go');
        $response->assertSee('Graphique');
        $response->assertSee('RTX 4060');
        $response->assertSee('Fiche Technique PDF');
        $response->assertSee('test.pdf');
    }
}
