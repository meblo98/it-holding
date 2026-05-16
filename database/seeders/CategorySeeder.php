<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Informatique',
                'description' => 'Matériel informatique, ordinateurs, composants et accessoires.',
                'subcategories' => [
                    ['name' => 'Ordinateurs portables', 'description' => 'PC portables, notebooks, MacBooks et ultrabooks.'],
                    ['name' => 'Ordinateurs de bureau', 'description' => 'Unités centrales et PC de bureau complets.'],
                    ['name' => 'PC Gamer', 'description' => 'Ordinateurs et configurations optimisés pour les jeux vidéo.'],
                    ['name' => 'Stations de travail', 'description' => 'Workstations professionnelles pour calcul intensif et graphisme.'],
                    ['name' => 'Écrans & moniteurs', 'description' => 'Moniteurs pour PC, dalles gaming et écrans professionnels.'],
                    ['name' => 'Imprimantes & scanners', 'description' => 'Imprimantes laser, jet d\'encre et scanners multifonctions.'],
                    ['name' => 'Claviers & souris', 'description' => 'Périphériques d\'entrée avec ou sans fil.'],
                    ['name' => 'Stockage (SSD, HDD, USB)', 'description' => 'Disques durs internes/externes, SSD, clés USB et cartes mémoire.'],
                    ['name' => 'Composants informatiques', 'description' => 'Processeurs, cartes graphiques, RAM, cartes mères, boîtiers et refroidissement.'],
                    ['name' => 'Réseaux & connectivité', 'description' => 'Routeurs, switchs, câbles ethernet, adaptateurs et matériel réseau.'],
                    ['name' => 'Serveurs', 'description' => 'Matériel serveur, racks et stockage réseau NAS.'],
                    ['name' => 'Logiciels & licences', 'description' => 'Systèmes d\'exploitation, suites bureautiques, antivirus et licences professionnelles.'],
                    ['name' => 'Accessoires informatiques', 'description' => 'Sacoches, housses, supports de refroidissement, câblage et hub USB.'],
                    ['name' => 'Onduleurs & alimentation', 'description' => 'Alimentations PC, onduleurs et multiprises sécurisées.'],
                ]
            ],
            [
                'name' => 'Électronique',
                'description' => 'Appareils électroniques grand public, smartphones et connectique.',
                'subcategories' => [
                    ['name' => 'Smartphones', 'description' => 'Téléphones portables intelligents iOS, Android.'],
                    ['name' => 'Tablettes', 'description' => 'Tablettes tactiles et liseuses.'],
                    ['name' => 'Téléviseurs', 'description' => 'TV LED, OLED, QLED et Smart TV.'],
                    ['name' => 'Audio (casques, écouteurs, haut-parleurs)', 'description' => 'Casques audio, écouteurs sans fil et enceintes bluetooth.'],
                    ['name' => 'Montres connectées', 'description' => 'Smartwatches et trackers d\'activité.'],
                    ['name' => 'Caméras & surveillance', 'description' => 'Caméras de sécurité, caméras IP et systèmes de vidéosurveillance.'],
                    ['name' => 'Consoles & gaming', 'description' => 'Consoles de jeux de salon et portables, manettes et accessoires de jeu.'],
                    ['name' => 'Chargeurs & batteries', 'description' => 'Chargeurs rapides, câbles de charge et batteries externes (Powerbanks).'],
                    ['name' => 'Accessoires mobiles', 'description' => 'Coques, protections d\'écran, supports voiture et stylets.'],
                    ['name' => 'Gadgets électroniques', 'description' => 'Objets connectés, gadgets innovants et accessoires divers.'],
                    ['name' => 'Éclairage & LED', 'description' => 'Ampoules connectées, rubans LED et luminaires intelligents.'],
                    ['name' => 'Sécurité électronique', 'description' => 'Alarmes sans fil, détecteurs et serrures connectées.'],
                ]
            ],
            [
                'name' => 'Papeterie & Fournitures de bureau',
                'description' => 'Fournitures scolaires et de bureau, consommables et mobilier léger.',
                'subcategories' => [
                    ['name' => 'Cahiers & blocs-notes', 'description' => 'Cahiers, carnets de notes, blocs et répertoires.'],
                    ['name' => 'Stylos & marqueurs', 'description' => 'Stylos à bille, feutres, surligneurs, marqueurs et crayons.'],
                    ['name' => 'Papier & ramettes', 'description' => 'Ramettes de papier A4/A3, papier photo et enveloppes.'],
                    ['name' => 'Classeurs & archivage', 'description' => 'Classeurs, chemises cartonnées, boîtes d\'archive et trieurs.'],
                    ['name' => 'Agendas & calendriers', 'description' => 'Agendas professionnels, calendriers muraux et de bureau.'],
                    ['name' => 'Fournitures scolaires', 'description' => 'Trousses, règles, gommes, ciseaux, colles et matériel de dessin.'],
                    ['name' => 'Matériel bureautique', 'description' => 'Plastifieuses, destructeurs de documents, agrafeuses et perforatrices.'],
                    ['name' => 'Calculatrices', 'description' => 'Calculatrices de bureau, scientifiques et graphiques.'],
                    ['name' => 'Enveloppes & impressions', 'description' => 'Enveloppes de toutes tailles, pochettes d\'expédition et étiquettes.'],
                    ['name' => 'Mobilier de bureau', 'description' => 'Chaises de bureau ergonomiques, lampes de bureau et petits rangements.'],
                    ['name' => 'Accessoires administratifs', 'description' => 'Trombonnes, punaises, rubans adhésifs et organiseurs de bureau.'],
                ]
            ],
        ];

        foreach ($categories as $catData) {
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($catData['name'])],
                [
                    'name' => $catData['name'],
                    'description' => $catData['description'],
                    'parent_id' => null,
                ]
            );

            foreach ($catData['subcategories'] as $subcatData) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($subcatData['name'])],
                    [
                        'name' => $subcatData['name'],
                        'description' => $subcatData['description'],
                        'parent_id' => $parent->id,
                    ]
                );
            }
        }
    }
}
