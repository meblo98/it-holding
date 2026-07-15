<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $pcCategory = Category::where('name', 'Ordinateurs portables')->first();
        if (!$pcCategory) {
            $pcCategory = Category::create([
                'name' => 'Ordinateurs portables',
                'slug' => 'ordinateurs-portables',
                'description' => 'PC portables et notebooks.'
            ]);
        }

        $dell = Brand::where('name', 'Dell')->first();
        if (!$dell) {
            $dell = Brand::create(['name' => 'Dell', 'slug' => 'dell']);
        }

        $apple = Brand::where('name', 'Apple')->first();
        if (!$apple) {
            $apple = Brand::create(['name' => 'Apple', 'slug' => 'apple']);
        }

        $products = [
            [
                'name' => 'Dell XPS 15 Bespoke Edition',
                'slug' => 'dell-xps-15-bespoke',
                'description' => 'Un ultra-portable haut de gamme entièrement configurable pour les professionnels exigeants.',
                'price' => 1200000.00,
                'purchase_price' => 900000.00,
                'stock' => 15,
                'category_id' => $pcCategory->id,
                'brand_id' => $dell->id,
                'condition' => 'new',
                'active' => true,
                'options' => [
                    ['name' => 'Mémoire RAM', 'value' => '8 Go DDR5', 'price' => 0.00],
                    ['name' => 'Mémoire RAM', 'value' => '16 Go DDR5', 'price' => 50000.00],
                    ['name' => 'Mémoire RAM', 'value' => '32 Go DDR5', 'price' => 120000.00],
                    ['name' => 'Stockage SSD', 'value' => '512 Go NVMe', 'price' => 0.00],
                    ['name' => 'Stockage SSD', 'value' => '1 To NVMe', 'price' => 45000.00],
                    ['name' => 'Stockage SSD', 'value' => '2 To NVMe', 'price' => 110000.00],
                    ['name' => 'Processeur', 'value' => 'Intel Core i5', 'price' => 0.00],
                    ['name' => 'Processeur', 'value' => 'Intel Core i7', 'price' => 90000.00],
                    ['name' => 'Processeur', 'value' => 'Intel Core i9', 'price' => 210000.00],
                ]
            ],
            [
                'name' => 'MacBook Pro 16 M3 Max',
                'slug' => 'macbook-pro-16-m3-max',
                'description' => 'Le notebook pro ultime d\'Apple, disponible avec options de mémoire unifiée et de stockage SSD.',
                'price' => 2500000.00,
                'purchase_price' => 2000000.00,
                'stock' => 10,
                'category_id' => $pcCategory->id,
                'brand_id' => $apple->id,
                'condition' => 'new',
                'active' => true,
                'options' => [
                    ['name' => 'Mémoire Unifiée', 'value' => '18 Go', 'price' => 0.00],
                    ['name' => 'Mémoire Unifiée', 'value' => '36 Go', 'price' => 240000.00],
                    ['name' => 'Mémoire Unifiée', 'value' => '96 Go', 'price' => 600000.00],
                    ['name' => 'Stockage SSD', 'value' => '512 Go', 'price' => 0.00],
                    ['name' => 'Stockage SSD', 'value' => '1 To', 'price' => 120000.00],
                    ['name' => 'Stockage SSD', 'value' => '2 To', 'price' => 300000.00],
                ]
            ]
        ];

        foreach ($products as $pData) {
            $options = $pData['options'];
            unset($pData['options']);

            $product = Product::updateOrCreate(
                ['slug' => $pData['slug']],
                $pData
            );

            // Clear old options if any
            $product->options()->delete();

            foreach ($options as $opt) {
                ProductOption::create([
                    'product_id' => $product->id,
                    'name' => $opt['name'],
                    'value' => $opt['value'],
                    'price' => $opt['price'],
                ]);
            }
        }
    }
}
