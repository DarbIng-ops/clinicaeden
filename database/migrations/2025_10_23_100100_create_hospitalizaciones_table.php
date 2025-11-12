<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospitalizaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('habitacion_id')->constrained('habitaciones')->onDelete('cascade');
            $table->foreignId('medico_general_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('jefe_enfermeria_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('auxiliar_enfermeria_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('fecha_ingreso');
            $table->dateTime('fecha_egreso')->nullable();
            $table->enum('estado', ['activo', 'alta_medica', 'alta_enfermeria', 'alta_pago', 'completado']);
            $table->text('motivo_hospitalizacion');
            $table->text('observaciones')->nullable();
            $table->decimal('costo_total', 10, 2)->default(0);
            $table->boolean('pago_completado')->default(false);
            $table->foreignId('consulta_id')->nullable()->constrained('consultas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospitalizaciones');
    }
};
