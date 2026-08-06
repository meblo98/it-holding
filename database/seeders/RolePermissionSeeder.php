<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RolePermission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPermissions = [
            'dg' => [
                'services', 'projects', 'posts', 'products', 'orders', 'quotes', 'invoices',
                'delivery-notes', 'suppliers', 'stock', 'clients', 'warranties', 'tickets',
                'chat', 'contracts', 'care', 'expenses', 'finance', 'reports', 'users'
            ],
            'commercial' => [
                'services', 'projects', 'products', 'orders', 'quotes', 'clients', 'warranties', 'chat'
            ],
            'comptable' => [
                'quotes', 'invoices', 'expenses', 'finance', 'reports'
            ],
            'magasinier' => [
                'products', 'orders', 'delivery-notes', 'suppliers', 'stock'
            ],
            'technicien' => [
                'warranties', 'tickets', 'contracts', 'care'
            ],
            'livreur' => [
                'delivery-notes'
            ],
        ];

        foreach ($defaultPermissions as $role => $perms) {
            RolePermission::updateOrCreate(
                ['role' => $role],
                ['permissions' => $perms]
            );
        }
    }
}
