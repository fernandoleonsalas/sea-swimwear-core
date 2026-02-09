<?php

namespace Database\Seeders;

use App\Models\User; // Asegúrate de importar el modelo
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un usuario de prueba
        User::create([
            'name'     => 'Administrador',
            'email'    => 'seaswinwear.vzla@gmail.com',
            'password' => Hash::make('Admin123456.'), // Siempre encriptar
            'role_id'  => '1'
        ]);
    }
}
