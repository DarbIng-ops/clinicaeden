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
        Schema::table('pacientes', function (Blueprint $table) {
            // Agregar constraint UNIQUE para DNI
            $table->unique('dni', 'uk_pacientes_dni');
            
            // Agregar índices para búsquedas rápidas
            $table->index('nombres', 'idx_pacientes_nombres');
            $table->index('apellidos', 'idx_pacientes_apellidos');
            $table->index('activo', 'idx_pacientes_activo');
            
            // Modificar columnas para agregar NOT NULL constraints
            $table->string('nombres')->nullable(false)->change();
            $table->string('apellidos')->nullable(false)->change();
            $table->string('telefono')->nullable(false)->change();
            $table->string('contacto_emergencia_nombre')->nullable(false)->change();
            $table->string('contacto_emergencia_telefono')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            // Eliminar índices
            $table->dropIndex('uk_pacientes_dni');
            $table->dropIndex('idx_pacientes_nombres');
            $table->dropIndex('idx_pacientes_apellidos');
            $table->dropIndex('idx_pacientes_activo');
            
            // Revertir columnas a nullable
            $table->string('nombres')->nullable()->change();
            $table->string('apellidos')->nullable()->change();
            $table->string('telefono')->nullable()->change();
            $table->string('contacto_emergencia_nombre')->nullable()->change();
            $table->string('contacto_emergencia_telefono')->nullable()->change();
        });
    }
};