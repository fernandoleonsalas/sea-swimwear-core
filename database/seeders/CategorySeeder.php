<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder; // <--- ¡ESTA LÍNEA ES FUNDAMENTAL!
use Illuminate\Support\Str;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents; // Si usas Laravel 9+

class CategorySeeder extends Seeder
{
    // Nota: El uso de 'WithoutModelEvents' se puede añadir si no quieres disparar observers
    // use WithoutModelEvents; 

    public function run(): void
    {
        $categories = ['Traje' => 'Traje', 'Enterizos' => 'enterizos', 'Accesorios' => 'accesorios', 'Traje de Lujo' => 'traje-de-lujo','Destacado' => 'destacado', 'Oferta' => 'oferta'];

        foreach ($categories as $key => $value) {
            Category::create([
                'name' => $key,
                'slug' => Str::slug($value),
            ]);
        }
    }
}