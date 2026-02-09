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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products'); /* definir la llave foránea, asumiendo la convención. */
            $table->string('full_sku')->comment('Ej: BIK-001-R-S');
            $table->integer('stock')->default('0')->comment('Stock gestionado a nivel de variante');
            $table->string('main_image_url')->nullable(true)->comment('Imagen del producto');
            $table->enum('status', ['active', 'inactive'])->default('active'); // estado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
