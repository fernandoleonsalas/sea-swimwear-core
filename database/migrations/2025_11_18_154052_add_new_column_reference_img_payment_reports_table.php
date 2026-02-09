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
        Schema::table('payment_reports', function (Blueprint $table) {
            // Agregar una nueva columna
            $table->string('reference_img')->nullable()
            ->after('reference'); // <-- Posiciona después de 'reference'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_reports', function (Blueprint $table) {
            // Eliminar la columna que acabas de añadir
            $table->dropColumn('reference_img');
        });
    }
};
