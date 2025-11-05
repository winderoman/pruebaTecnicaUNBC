<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuario administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
        ]);

        // Usuario de prueba
        User::create([
            'name' => 'Usuario Demo',
            'email' => 'demo@gmail.com',
            'password' => Hash::make('demo'),
        ]);
    }
}