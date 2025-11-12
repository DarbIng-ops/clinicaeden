<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historia_clinica_id')->constrained('historias_clinicas')->onDelete('cascade');
            $table->foreignId('cita_id')->nullable()->constrained('citas')->onDelete('set null');
            $table->foreignId('medico_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->dateTime('fecha_consulta');
            $table->text('motivo');
            $table->text('sintomas')->nullable();
            $table->text('diagnostico')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};