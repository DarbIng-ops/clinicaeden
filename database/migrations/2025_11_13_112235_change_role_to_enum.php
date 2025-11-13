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
        // Primero, actualizar todos los usuarios con role='recepcion' a 'recepcionista'
        DB::table('users')
            ->where('role', 'recepcion')
            ->update(['role' => 'recepcionista']);

        // Cambiar la columna role a ENUM
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'medico_general', 'medico_especialista', 'recepcionista', 'auxiliar_enfermeria', 'jefe_enfermeria', 'caja') NOT NULL DEFAULT 'recepcionista'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Volver la columna role a VARCHAR/STRING
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(255) NOT NULL DEFAULT 'recepcionista'");
    }
};
