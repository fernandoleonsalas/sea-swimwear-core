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
        Schema::create('configurations', function (Blueprint $table) {
            $table->string('key', 50)->primary(); 
            // Define la columna 'value' como TEXT, NOT NULL y añade un comentario.
            // En Laravel, puedes usar el método ->comment() para añadir la descripción.
            $table->text('text')->nullable(true)->comment('Ej: MIN_PIEZAS_MAYORISTA, o precio del dolar'); 
            $table->decimal('value', 10, 2)->nullable(true)->comment('precio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
