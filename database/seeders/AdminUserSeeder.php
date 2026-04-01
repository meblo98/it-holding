<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email    = env('ADMIN_EMAIL', 'admin@itholding.sn');
        $password = env('ADMIN_PASSWORD');

        if (!$password) {
            $this->command->error('ADMIN_PASSWORD non défini dans .env — admin non créé.');
            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name'     => 'Admin IT Holding',
                'password' => Hash::make($password),
                'is_admin' => true,
            ]
        );

        $this->command->info('Utilisateur Admin créé/mis à jour.');
        $this->command->info('Email: ' . $email);
    }
}
