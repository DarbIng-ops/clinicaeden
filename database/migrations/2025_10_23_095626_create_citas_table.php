<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('medico_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recepcionista_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('fecha');
            $table->time('hora');
            $table->text('motivo_consulta');
            $table->enum('estado', ['pendiente', 'confirmada', 'en_curso', 'completada', 'cancelada'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->foreignId('cancelada_por')->nullable()->constrained('users')->onDelete('set null');
            $table->text('motivo_cancelacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};