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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku_base')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price_retail', 10, 2); // precio_detal
            $table->decimal('price_wholesale', 10, places: 2)->nullable(); // precio_mayor
            $table->text('min_pieces')->nullable()->comment('Ej: MIN_PIEZAS_MAYORISTA, 6'); 
            $table->enum('status', ['active', 'inactive'])->default('active'); // estado
            // No incluimos stock_total aquí, será un valor calculado de todas las variantes.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

