<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            // La cédula es la clave primaria (PK)
            // la cédula es un string, pero si es solo numérica, puedes usar `string` o `bigInteger`.
            // Para asegurar unicidad y que sea PK:
            $table->string('cedula', 20)->primary(); 

            // Otros campos solicitados
            $table->string('names', 100);
            $table->string('email', 100)->unique(); // El correo suele ser único
            $table->string('phone', 20)->nullable(); // Se asume que el teléfono puede ser opcional
            $table->string('address', 255)->nullable(); // Se asume que la dirección puede ser opcional
            
            // Tiempos de registro y actualización
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
