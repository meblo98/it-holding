<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            // Informatique
            ['name' => 'HP', 'description' => 'Matériel informatique, ordinateurs portables, serveurs et accessoires.'],
            ['name' => 'Dell', 'description' => 'Ordinateurs, serveurs et solutions informatiques professionnelles.'],
            ['name' => 'Lenovo', 'description' => 'PC portables, ordinateurs de bureau et serveurs.'],
            ['name' => 'Apple', 'description' => 'Smartphones, tablettes, ordinateurs et accessoires haut de gamme.'],
            ['name' => 'ASUS', 'description' => 'Ordinateurs portables, cartes mères et matériel pour gamers.'],
            ['name' => 'Acer', 'description' => 'Ordinateurs portables, écrans et vidéoprojecteurs.'],
            ['name' => 'Toshiba', 'description' => 'Matériel informatique et solutions de stockage.'],
            ['name' => 'MSI', 'description' => 'PC gamers, cartes mères, cartes graphiques et moniteurs.'],
            ['name' => 'Canon', 'description' => 'Solutions d\'impression, scanners et matériel photographique.'],
            ['name' => 'Epson', 'description' => 'Imprimantes, vidéoprojecteurs et scanners professionnels.'],

            // Smartphones & Téléphonie
            ['name' => 'Samsung', 'description' => 'Smartphones, tablettes, téléviseurs et électronique grand public.'],
            ['name' => 'Tecno', 'description' => 'Smartphones et appareils mobiles innovants.'],
            ['name' => 'Infinix', 'description' => 'Smartphones, accessoires et appareils connectés.'],
            ['name' => 'Xiaomi', 'description' => 'Smartphones, domotique et électronique grand public.'],
            ['name' => 'Redmi', 'description' => 'Gamme de smartphones et d\'accessoires abordables.'],
            ['name' => 'Huawei', 'description' => 'Smartphones, tablettes et équipements de réseau.'],
            ['name' => 'Oppo', 'description' => 'Smartphones et appareils mobiles.'],
            ['name' => 'Vivo', 'description' => 'Smartphones et accessoires connectés.'],
            ['name' => 'Nokia', 'description' => 'Téléphones portables, smartphones et équipements de télécommunications.'],

            // Accessoires & Électronique
            ['name' => 'Logitech', 'description' => 'Périphériques informatiques : souris, claviers, webcams, audio.'],
            ['name' => 'JBL', 'description' => 'Matériel audio, enceintes Bluetooth et casques.'],
            ['name' => 'Sony', 'description' => 'Électronique grand public, consoles, audio et vidéo.'],
            ['name' => 'Oraimo', 'description' => 'Accessoires mobiles, chargeurs et écouteurs sans fil.'],
            ['name' => 'Anker', 'description' => 'Batteries externes, chargeurs et câbles haut de gamme.'],
            ['name' => 'Vention', 'description' => 'Câbles, adaptateurs et connectivité audiovisuelle.'],
            ['name' => 'Baseus', 'description' => 'Accessoires électroniques, chargeurs rapides et supports.'],

            // Réseaux, sécurité & entreprises
            ['name' => 'Cisco', 'description' => 'Équipements réseau professionnels et solutions de sécurité.'],
            ['name' => 'TP-Link', 'description' => 'Routeurs, switchs et équipements réseau grand public et pro.'],
            ['name' => 'MikroTik', 'description' => 'Équipements de réseau filaire et sans fil.'],
            ['name' => 'ZKTeco', 'description' => 'Solutions de contrôle d\'accès, pointage et sécurité biométrique.'],
            ['name' => 'Ubiquiti', 'description' => 'Produits de communication réseau sans fil professionnels.'],
            ['name' => 'Hikvision', 'description' => 'Caméras de vidéosurveillance et systèmes de sécurité.'],
            ['name' => 'Dahua', 'description' => 'Solutions de vidéosurveillance et sécurité électronique.'],
            ['name' => 'Brother', 'description' => 'Imprimantes, étiqueteuses et solutions bureautiques.'],
            ['name' => 'Microsoft', 'description' => 'Logiciels, systèmes d\'exploitation et périphériques informatiques.'],
            ['name' => 'Intel', 'description' => 'Processeurs, composants et solutions informatiques avancées.'],
        ];

        foreach ($brands as $brandData) {
            Brand::updateOrCreate(
                ['slug' => Str::slug($brandData['name'])],
                [
                    'name' => $brandData['name'],
                    'description' => $brandData['description'],
                ]
            );
        }
    }
}
