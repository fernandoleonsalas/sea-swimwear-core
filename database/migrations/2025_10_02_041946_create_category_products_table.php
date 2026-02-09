<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Por convención de Laravel, las tablas pivot usan los nombres singulares de 
        // las dos tablas que unen, ordenados alfabéticamente, y separados por un guion bajo.
        // El nombre ideal sería 'category_product'.
        Schema::create('category_product', function (Blueprint $table) {
            
            // 1. Definición de la primera clave foránea (product_id)
            $table->unsignedBigInteger('product_id'); 
            
            // 2. Definición de la segunda clave foránea (category_id)
            $table->unsignedBigInteger('category_id');  

            // 3. Establece la Clave Primaria Compuesta
            // Esto asegura que una combinación de producto y categoría solo exista una vez.
            $table->primary(['product_id', 'category_id']);
            
            // 4. Definición de las Restricciones de Clave Foránea
            // Esto asegura la integridad referencial (que los IDs existan en sus tablas).
            $table->foreign('product_id')
                ->references('id')->on('products')
                ->onDelete('cascade'); // Opcional: si se elimina un producto, se elimina la relación
                
            $table->foreign('category_id')
                ->references('id')->on('categories')
                ->onDelete('cascade'); // Opcional: si se elimina una categoría, se elimina la relación
                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_product');
    }
};
