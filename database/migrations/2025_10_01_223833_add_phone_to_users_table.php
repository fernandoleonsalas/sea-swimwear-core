<?php
// EJEMPLO DE COMO ACTUALIZAR UNA TABLA DESDE LA MIGRACION
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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email_verified_at'); /* columna 'telefono' */
            $table->foreignId('role_id')
            // ->default("1") // si desea definir el valor po defecto
            ->after('remember_token') // definir la posicion
            ->constrained('roles'); /* definir la llave foránea, asumiendo la convención. */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']); // Se recomienda eliminar la clave foránea primero
            $table->dropColumn('phone'); // Se recomienda eliminar la columna
            $table->dropColumn('role_id'); // Se recomienda eliminar la columna
        });
    }
};
