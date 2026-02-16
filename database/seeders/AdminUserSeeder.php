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
        User::create([
            'name' => 'Admin IT Holding',
            'email' => 'admin@itholding.sn',
            'password' => Hash::make('password'), // Mot de passe par défaut
            'is_admin' => true,
        ]);
        
        $this->command->info('Utilisateur Admin créé avec succès.');
        $this->command->info('Email: admin@itholding.sn');
        $this->command->info('Mot de passe: password');
    }
}
