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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->dateTime('order_date')->default(DB::raw('CURRENT_TIMESTAMP'));
             // -- Campo para la clave foránea: debe ser del mismo tipo que la columna a la que apunta (cedula: string)
            $table->string('client_cedula', 20)
                ->nullable() // Permitimos que sea NULL si la compra es de un invitado
                ->index()
                ->comment('FK cedula del cliente.');
            // Flujo de Pago Flexible (RF-04)
            $table->decimal('total_purchase', 10, 2)->comment('Compra total');
            $table->decimal('total_paid', 10, 2)->default(0.00)->comment('Total Pagado');
            $table->decimal('remaining_amount', 10, 2)->comment('cantidad restante');
            $table->decimal('dollar_rate', 10, 2)->comment('tasa del dolar');
            // -- Estados (RF-08)
            $table->enum('order_status', ['Por Verificar', 'Confirmado', 'Listo para entregar', 'Entregado', 'Cancelado'])->default('Por Verificar');
            $table->enum('deposit_status', ['50%', '100%', 'Pendiente 50%', 'Por Verificar'])->default('Pendiente 50%');
            // Lógica de Cancelación y Segundo Pago (RN-04, RF-05)
            $table->string('token_segundo_pago', 100)->nullable()->unique()->comment('Enlace único');
            $table->dateTime('payment_deadline')->nullable()->comment('fecha límite para el segundo pago');
            $table->timestamps();
            
            // 2. Restricción de Clave Foránea (FK)
            $table->foreign('client_cedula')
                ->references('cedula')->on('clients') // Apunta a la columna 'cedula' de la tabla 'clients'
                ->onUpdate('cascade') // Opcional: si la cédula cambia, se actualiza en orders
                ->onDelete('set null'); // Si el cliente es eliminado, el pedido se mantiene con 'client_cedula' = NULL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
