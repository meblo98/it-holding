<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\ProductOption;
use App\Models\ProductImage;
use App\Models\PackItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminProductDuplicateTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $normalUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@itholding.sn',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_admin' => true,
        ]);

        // Create normal user
        $this->normalUser = User::create([
            'name' => 'Normal User',
            'email' => 'user@itholding.sn',
            'password' => bcrypt('password'),
            'role' => 'user',
            'is_admin' => false,
        ]);

        Storage::fake('public');
    }

    /** @test */
    public function admin_can_duplicate_simple_product()
    {
        // 1. Create a product with files and options
        $imageFile = UploadedFile::fake()->image('test_product.jpg');
        $pdfFile = UploadedFile::fake()->create('tech_sheet.pdf', 100);

        $imagePath = Storage::disk('public')->putFile('products', $imageFile);
        $pdfPath = Storage::disk('public')->putFile('products/tech_sheets', $pdfFile);

        $product = Product::create([
            'name' => 'Original Product',
            'slug' => 'original-product',
            'description' => 'Original Description',
            'purchase_price' => 12000,
            'price' => 19000,
            'stock' => 10,
            'warranty_duration_months' => 24,
            'image' => $imagePath,
            'fiche_technique' => $pdfPath,
            'active' => true,
            'condition' => 'Neuf',
        ]);

        // Add options
        ProductOption::create([
            'product_id' => $product->id,
            'name' => 'Couleur',
            'value' => 'Noir',
            'price' => 0.00,
        ]);

        // Add additional images
        $galleryFile = UploadedFile::fake()->image('gallery1.jpg');
        $galleryPath = Storage::disk('public')->putFile('products', $galleryFile);
        ProductImage::create([
            'product_id' => $product->id,
            'path' => $galleryPath,
            'sort_order' => 1,
        ]);

        // 2. Perform duplication
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.products.duplicate', $product->id));

        // 3. Assertions
        $duplicatedProduct = Product::where('name', 'Original Product (Copie)')->first();
        $this->assertNotNull($duplicatedProduct);

        $response->assertRedirect(route('admin.products.edit', $duplicatedProduct->id));
        $response->assertSessionHas('success');

        // Check attributes copied
        $this->assertEquals('Original Description', $duplicatedProduct->description);
        $this->assertEquals(12000, $duplicatedProduct->purchase_price);
        $this->assertEquals(19000, $duplicatedProduct->price);
        $this->assertEquals(10, $duplicatedProduct->stock);
        $this->assertEquals(24, $duplicatedProduct->warranty_duration_months);
        $this->assertEquals('Neuf', $duplicatedProduct->condition);
        $this->assertTrue((bool)$duplicatedProduct->active);
        $this->assertNotEquals('original-product', $duplicatedProduct->slug);

        // Check files copied physically
        $this->assertNotEmpty($duplicatedProduct->image);
        $this->assertNotEquals($product->image, $duplicatedProduct->image);
        Storage::disk('public')->assertExists($duplicatedProduct->image);

        $this->assertNotEmpty($duplicatedProduct->fiche_technique);
        $this->assertNotEquals($product->fiche_technique, $duplicatedProduct->fiche_technique);
        Storage::disk('public')->assertExists($duplicatedProduct->fiche_technique);

        // Check options copied
        $this->assertCount(1, $duplicatedProduct->options);
        $this->assertEquals('Couleur', $duplicatedProduct->options->first()->name);
        $this->assertEquals('Noir', $duplicatedProduct->options->first()->value);

        // Check gallery images copied physically
        $this->assertCount(1, $duplicatedProduct->images);
        $newGalleryPath = $duplicatedProduct->images->first()->path;
        $this->assertNotEquals($galleryPath, $newGalleryPath);
        Storage::disk('public')->assertExists($newGalleryPath);
    }

    /** @test */
    public function admin_can_duplicate_pack_product()
    {
        // Create components
        $component1 = Product::create([
            'name' => 'Comp 1',
            'slug' => 'comp-1',
            'description' => 'Desc 1',
            'purchase_price' => 5000,
            'price' => 8000,
            'stock' => 20,
            'active' => true,
        ]);

        $pack = Product::create([
            'name' => 'Original Pack',
            'slug' => 'original-pack',
            'description' => 'Original Pack Desc',
            'purchase_price' => 4500,
            'price' => 7500,
            'active' => true,
            'is_pack' => true,
        ]);

        PackItem::create([
            'pack_id' => $pack->id,
            'product_id' => $component1->id,
            'quantity' => 2,
        ]);

        // Duplicate
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.products.duplicate', $pack->id));

        $duplicatedPack = Product::where('name', 'Original Pack (Copie)')->first();
        $this->assertNotNull($duplicatedPack);
        $this->assertTrue((bool)$duplicatedPack->is_pack);

        // Assert pack items duplicated
        $this->assertCount(1, $duplicatedPack->packItems);
        $this->assertEquals($component1->id, $duplicatedPack->packItems->first()->product_id);
        $this->assertEquals(2, $duplicatedPack->packItems->first()->quantity);
    }

    /** @test */
    public function normal_user_cannot_duplicate_product()
    {
        $product = Product::create([
            'name' => 'Protected Product',
            'slug' => 'protected-product',
            'description' => 'Description',
            'purchase_price' => 1000,
            'price' => 1500,
            'active' => true,
        ]);

        $response = $this->actingAs($this->normalUser)
            ->post(route('admin.products.duplicate', $product->id));

        // Normal user should be redirected / forbidden (since it is behind admin/products routes with permission check)
        $response->assertStatus(403);
        $this->assertNull(Product::where('name', 'Protected Product (Copie)')->first());
    }
}
