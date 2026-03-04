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
        // Actualizar usuarios con role='recepcion' a 'recepcionista' (compatible con todos los DBs)
        DB::table('users')
            ->where('role', 'recepcion')
            ->update(['role' => 'recepcionista']);

        // MySQL soporta ENUM; SQLite almacena como texto y omite este cambio de tipo
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'medico_general', 'medico_especialista', 'recepcionista', 'auxiliar_enfermeria', 'jefe_enfermeria', 'caja') NOT NULL DEFAULT 'recepcionista'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(255) NOT NULL DEFAULT 'recepcionista'");
        }
    }
};
