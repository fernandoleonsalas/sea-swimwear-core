<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Llama al Seeder de atributos
        $this->call(AttributeSeeder::class);

        // Llama al Seeder de Categorías
        $this->call(CategorySeeder::class);
        
        // Llama al Seeder de Roles
        $this->call(RoleSeeder::class);
        
        // Llama al Seeder de user
        $this->call(UserSeeder::class);
    }
}
