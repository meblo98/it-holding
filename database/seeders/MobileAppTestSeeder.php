<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Client;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\Ticket;
use App\Models\Warranty;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\WalletTransaction;

class MobileAppTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Categories & Brands
        $networkCat = Category::updateOrCreate(
            ['slug' => 'equipements-reseaux'],
            ['name' => 'Équipements Réseaux', 'description' => 'Routeurs, switches et points d\'accès.']
        );

        $storageCat = Category::updateOrCreate(
            ['slug' => 'stockage'],
            ['name' => 'Stockage & Serveurs', 'description' => 'NAS, disques et serveurs physiques.']
        );

        $cisco = Brand::updateOrCreate(['slug' => 'cisco'], ['name' => 'Cisco']);
        $ubiquiti = Brand::updateOrCreate(['slug' => 'ubiquiti'], ['name' => 'Ubiquiti']);
        $synology = Brand::updateOrCreate(['slug' => 'synology'], ['name' => 'Synology']);

        // 2. Seed Extra Products
        $products = [
            [
                'name' => 'Routeur Cisco ISR 4331',
                'slug' => 'routeur-cisco-isr-4331',
                'description' => 'Routeur modulaire d\'entreprise Cisco avec débit agrégé de 100 Mbps à 300 Mbps.',
                'price' => 950000.00,
                'purchase_price' => 750000.00,
                'stock' => 2,
                'category_id' => $networkCat->id,
                'brand_id' => $cisco->id,
                'condition' => 'new',
                'active' => true,
                'wholesale_qty' => 5,
                'wholesale_discount_rate' => 10.00,
            ],
            [
                'name' => 'Switch Ubiquiti UniFi 24 Ports PoE',
                'slug' => 'switch-ubiquiti-unifi-24-poe',
                'description' => 'Switch PoE Gigabit administrable de couche 2 avec 24 ports RJ45 et 2 ports SFP.',
                'price' => 380000.00,
                'purchase_price' => 290000.00,
                'stock' => 15,
                'category_id' => $networkCat->id,
                'brand_id' => $ubiquiti->id,
                'condition' => 'new',
                'active' => true,
                'wholesale_qty' => 4,
                'wholesale_discount_rate' => 8.00,
            ],
            [
                'name' => 'Serveur NAS Synology 8 Baies DS1821+',
                'slug' => 'synology-nas-ds1821',
                'description' => 'NAS puissant à 8 baies optimisé pour la sauvegarde de données d\'entreprise.',
                'price' => 1100000.00,
                'purchase_price' => 900000.00,
                'stock' => 6,
                'category_id' => $storageCat->id,
                'brand_id' => $synology->id,
                'condition' => 'new',
                'active' => true,
                'wholesale_qty' => 3,
                'wholesale_discount_rate' => 5.00,
            ]
        ];

        foreach ($products as $pData) {
            Product::updateOrCreate(
                ['slug' => $pData['slug']],
                $pData
            );
        }

        // 3. Seed Users & Linked Profiles
        // Commercial
        $commercial = User::updateOrCreate(
            ['email' => 'commercial@itholding.sn'],
            [
                'name' => 'Assane Diouf',
                'password' => Hash::make('password'),
                'role' => 'commercial',
                'phone' => '+221 77 980 11 22',
            ]
        );

        // Technicien
        $technician = User::updateOrCreate(
            ['email' => 'technicien@itholding.sn'],
            [
                'name' => 'Cheikh Tidiane',
                'password' => Hash::make('password'),
                'role' => 'technicien',
                'phone' => '+221 76 543 21 09',
            ]
        );

        // Client User
        $clientUser = User::updateOrCreate(
            ['email' => 'client@gmail.com'],
            [
                'name' => 'Seydou Keita',
                'password' => Hash::make('password'),
                'role' => 'client',
                'phone' => '+221 77 123 45 67',
            ]
        );

        // 4. Seed Client Profiles
        $clientProfile = Client::updateOrCreate(
            ['user_id' => $clientUser->id],
            [
                'first_name' => 'Seydou',
                'last_name' => 'Keita',
                'company_name' => 'Keita Technologies',
                'email' => 'client@gmail.com',
                'phone' => '+221 77 123 45 67',
                'address' => 'Mermoz, Dakar',
                'city' => 'Dakar',
                'country' => 'Sénégal',
                'is_professional' => true,
                'wallet_balance' => 150000.00,
                'current_balance' => 45000.00,
                'credit_limit' => 1000000.00,
                'payment_terms' => '30 Jours',
            ]
        );

        // Additional CRM Client (not linked to a user account)
        $crmClient = Client::updateOrCreate(
            ['email' => 'fatou@sow.sn'],
            [
                'first_name' => 'Fatou',
                'last_name' => 'Sow',
                'company_name' => 'Boutique Sow',
                'phone' => '+221 76 892 11 44',
                'address' => 'Parcelles Assainies, U.21',
                'city' => 'Dakar',
                'country' => 'Sénégal',
                'is_professional' => true,
                'wallet_balance' => 25000.00,
                'current_balance' => 120000.00,
                'credit_limit' => 500000.00,
                'payment_terms' => 'Immédiat',
            ]
        );

        // 5. Seed Warranties, Orders, and Wallet Transactions
        $dellProduct = Product::where('slug', 'dell-xps-15-bespoke')->first();
        if ($dellProduct) {
            $warranty = Warranty::updateOrCreate(
                ['serial_number' => 'DXPS15-88392-SN'],
                [
                    'number' => Warranty::generateNumber(),
                    'client_id' => $clientProfile->id,
                    'product_id' => $dellProduct->id,
                    'product_name' => 'Dell XPS 15',
                    'client_name' => $clientProfile->full_name,
                    'client_phone' => $clientProfile->phone,
                    'purchase_date' => now(),
                    'duration_months' => 24,
                    'expiry_date' => now()->addMonths(12),
                    'status' => 'active',
                ]
            );
        }

        // Wallet Transaction
        WalletTransaction::updateOrCreate(
            ['client_id' => $clientProfile->id, 'amount' => 50000.00],
            [
                'type' => 'deposit',
                'description' => 'Recharge via Wave',
                'transaction_date' => now()->subDays(5),
            ]
        );

        // 6. Seed Tickets (SAV)
        $ticketCount = Ticket::count();
        if ($ticketCount == 0) {
            Ticket::create([
                'number' => 'SAV-2026-0012',
                'client_id' => $clientProfile->id,
                'client_name' => 'Seydou Keita',
                'client_phone' => '+221 77 123 45 67',
                'client_email' => 'client@gmail.com',
                'product_name' => 'Dell XPS 15',
                'serial_number' => 'DXPS15-88392-SN',
                'title' => 'Écran Dell scintille et chauffe',
                'description' => 'Le client signale un scintillement continu après 30 minutes de fonctionnement intensif. L\'appareil est chaud au toucher sur la partie inférieure.',
                'status' => 'in_progress',
                'priority' => 'urgent',
                'type' => 'repair',
                'assigned_to' => $technician->id,
                'opened_at' => now()->subDays(2),
            ]);

            Ticket::create([
                'number' => 'SAV-2026-0015',
                'client_id' => $clientProfile->id,
                'client_name' => 'Seydou Keita',
                'client_phone' => '+221 77 123 45 67',
                'client_email' => 'client@gmail.com',
                'product_name' => 'Cisco ISR 4331',
                'serial_number' => 'CSCO-ISR-99238',
                'title' => 'Routeur Cisco redémarre en boucle',
                'description' => 'Perte de connexion intermittente. Le routeur effectue un reboot de manière cyclique toutes les 5 minutes.',
                'status' => 'open',
                'priority' => 'high',
                'type' => 'repair',
                'opened_at' => now()->subHours(12),
            ]);
        }
    }
}
