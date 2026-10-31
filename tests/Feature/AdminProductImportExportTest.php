<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminProductImportExportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@itholding.sn',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_admin' => true,
        ]);
    }

    public function test_admin_can_export_products_as_json_and_csv(): void
    {
        Product::create([
            'name' => 'Laptop',
            'slug' => 'laptop',
            'description' => 'Ordinateur portable',
            'purchase_price' => 500000,
            'price' => 700000,
            'stock' => 5,
            'warranty_duration_months' => 12,
            'active' => true,
            'specs' => ['RAM' => '16 Go'],
        ]);

        $json = $this->actingAs($this->admin)->get(route('admin.products.export', 'json'));
        $json->assertOk()
            ->assertHeader('content-disposition', 'attachment; filename="produits.json"')
            ->assertJsonPath('products.0.slug', 'laptop')
            ->assertJsonPath('products.0.specs.RAM', '16 Go');

        $csv = $this->get(route('admin.products.export', 'csv'));
        $csv->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $csvContents = $csv->streamedContent();
        $this->assertStringContainsString('name;slug;description', $csvContents);

        $this->post(route('admin.products.import'), [
            'file' => UploadedFile::fake()->createWithContent('produits.csv', $csvContents),
        ])->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseCount('products', 1);
        $this->assertSame(['RAM' => '16 Go'], Product::where('slug', 'laptop')->first()->specs);
    }

    public function test_admin_can_import_json_and_update_a_product_by_slug(): void
    {
        Product::create([
            'name' => 'Ancien nom',
            'slug' => 'laptop',
            'description' => 'Ancienne description',
            'purchase_price' => 400000,
            'price' => 600000,
            'stock' => 2,
            'warranty_duration_months' => 12,
            'active' => true,
        ]);

        $file = UploadedFile::fake()->createWithContent('produits.json', json_encode([
            'products' => [
                [
                    'name' => 'Laptop mis à jour',
                    'slug' => 'laptop',
                    'description' => 'Nouvelle description',
                    'purchase_price' => 500000,
                    'price' => 700000,
                    'stock' => 5,
                    'warranty_duration_months' => 24,
                    'active' => true,
                    'specs' => ['RAM' => '16 Go'],
                ],
                [
                    'name' => 'Souris',
                    'slug' => 'souris',
                    'description' => 'Souris sans fil',
                    'purchase_price' => 5000,
                    'price' => 10000,
                    'stock' => 10,
                    'warranty_duration_months' => 12,
                ],
            ],
        ], JSON_UNESCAPED_UNICODE));

        $this->actingAs($this->admin)
            ->post(route('admin.products.import'), ['file' => $file])
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('products', 2);
        $this->assertDatabaseHas('products', [
            'slug' => 'laptop',
            'name' => 'Laptop mis à jour',
            'stock' => 5,
        ]);
        $this->assertDatabaseHas('products', ['slug' => 'souris', 'name' => 'Souris']);
        $this->assertSame(['RAM' => '16 Go'], Product::where('slug', 'laptop')->first()->specs);
    }

    public function test_admin_can_import_a_semicolon_delimited_csv_table(): void
    {
        $file = UploadedFile::fake()->createWithContent(
            'produits.csv',
            "name;slug;description;purchase_price;price;stock;warranty_duration_months\nCasque;casque;Audio;10000;15000;3;12\n"
        );

        $this->actingAs($this->admin)
            ->post(route('admin.products.import'), ['file' => $file])
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'slug' => 'casque',
            'name' => 'Casque',
            'purchase_price' => 10000,
            'price' => 15000,
        ]);
    }

    public function test_invalid_import_does_not_create_any_products(): void
    {
        $file = UploadedFile::fake()->createWithContent('produits.json', json_encode([
            [
                'name' => 'Produit valide',
                'slug' => 'valide',
                'description' => 'Description',
                'purchase_price' => 100,
                'price' => 150,
            ],
            [
                'name' => 'Produit invalide',
                'slug' => 'invalide',
                'description' => 'Description',
                'purchase_price' => -5,
                'price' => 150,
            ],
        ]));

        $this->actingAs($this->admin)
            ->from(route('admin.products.index'))
            ->post(route('admin.products.import'), ['file' => $file])
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHasErrors();

        $this->assertDatabaseCount('products', 0);
    }
}
