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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            // Tipo de cuenta: 'pago_movil', 'zelle', 'transferencia'
            $table->string('type'); 

            // Campos comunes y específicos
            $table->string('bank_name')->nullable();     // Nombre del banco o "Zelle"
            $table->string('holder_name');              // Nombre del titular
            $table->string('holder_id')->nullable();    // CI o RIF (Para Pago Móvil/Transferencia)
            $table->string('phone_number')->nullable(); // Teléfono (Para Pago Móvil/Zelle)
            $table->string('email')->nullable();        // Email (Para Zelle)
            $table->string('account_number')->nullable(); // Número de cuenta (Transferencia)
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
