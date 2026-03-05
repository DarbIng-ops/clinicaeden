<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: tabla de auditoría de accesos.
 *
 * Implementación conforme a ISO 27001 — Control A.12.4 (registro de eventos).
 * Los registros de auditoría NO se actualizan (sin updated_at).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria_accesos', function (Blueprint $table) {
            $table->id();

            // Usuario que realizó la acción (nullable: puede haber intentos sin sesión)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Tipo de acción registrada
            $table->string('accion', 100);

            // Modelo afectado (opcional)
            $table->string('modelo', 50)->nullable();
            $table->unsignedBigInteger('modelo_id')->nullable();

            // Datos de red
            $table->string('ip', 45);          // soporta IPv6
            $table->string('user_agent', 255);

            // Solo created_at — los logs son inmutables
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria_accesos');
    }
};
