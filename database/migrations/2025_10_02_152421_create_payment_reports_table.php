<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders'); /* definir la llave foránea, asumiendo la convención. */
            $table->decimal('amount', 10, 2);
            $table->decimal('dollar_rate', 10, 2)->comment('tasa del dolar');
            $table->string('method', 100)->comment('Ej: Zelle, Pago Móvil');
            $table->string('reference', 100)->comment('Número de referencia/transacción');
            $table->dateTime('report_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('status', allowed: ['Pendiente', 'Verificado', 'Rechazado'])->default('Pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_reports');
    }
};
