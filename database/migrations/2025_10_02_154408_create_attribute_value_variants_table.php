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
        // Por convención de Laravel, las tablas pivot usan los nombres singulares de 
        // las dos tablas que unen, ordenados alfabéticamente, y separados por un guion bajo.
        // El nombre ideal sería 'attribute_value_variants'.
        Schema::create('attribute_value_variants', function (Blueprint $table) {
            
            // 1. Definición de la primera clave foránea (product_variant_id)
            $table->unsignedBigInteger('product_variant_id'); 
            
            // 2. Definición de la segunda clave foránea (attribute_value_id)
            $table->unsignedBigInteger('attribute_value_id');  

            // 3. Establece la Clave Primaria Compuesta
            // Esto asegura que una combinación de producto variante y atributos solo exista una vez.
            $table->primary(['product_variant_id', 'attribute_value_id']);
            
            // 4. Definición de las Restricciones de Clave Foránea
            // Esto asegura la integridad referencial (que los IDs existan en sus tablas).
            $table->foreign('product_variant_id')
                ->references('id')->on('product_variants')
                ->onDelete('cascade'); // Opcional: si se elimina un producto, se elimina la relación
            $table->foreign('attribute_value_id')
                ->references('id')->on('attribute_values')
                ->onDelete('cascade'); // Opcional: si se elimina una categoría, se elimina la relación
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_value_variants');
    }
};
