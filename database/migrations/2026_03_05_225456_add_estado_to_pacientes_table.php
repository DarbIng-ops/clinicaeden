<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega columna `estado` a la tabla pacientes.
 * Gestiona el flujo clínico: activo → pendiente_salida → egresado.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->enum('estado', ['activo', 'pendiente_salida', 'egresado'])
                  ->default('activo')
                  ->after('activo');
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};
