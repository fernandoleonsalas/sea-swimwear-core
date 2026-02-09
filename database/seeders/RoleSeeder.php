<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder; // <--- ¡ESTA LÍNEA ES FUNDAMENTAL!
use Illuminate\Support\Str;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents; // Si usas Laravel 9+

class RoleSeeder extends Seeder
{
    // Nota: El uso de 'WithoutModelEvents' se puede añadir si no quieres disparar observers
    // use WithoutModelEvents; 

    public function run(): void
    {
        // Definir los roles que deseas crear
        $roles = ['administrador' => 'admin', 'usuario' => 'user'];
        foreach ($roles as $key => $value) {
            Role::create([
                'name' => $key,
            ]);
        }
    }
}