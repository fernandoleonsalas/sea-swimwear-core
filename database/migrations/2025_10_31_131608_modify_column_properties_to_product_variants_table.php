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
        Schema::table('product_variants', function (Blueprint $table) {
            // 1. Modificar la propiedad de la columna existente
            $table->unsignedBigInteger('image_id')->nullable()->change();
            // 2. Agregar la restricción de llave foránea.
            $table->foreign('image_id')->references('id')->on('images');  /* definir la llave foránea, asumiendo la convención. */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // 1. Eliminar la restricción de llave foránea
            $table->dropForeign(['image_id']); 
            // 2. Revertir la propiedad
            $table->unsignedBigInteger('image_id')->nullable(false)->change();
        });
    }
};
