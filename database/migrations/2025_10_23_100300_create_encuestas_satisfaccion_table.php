<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encuestas_satisfaccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('hospitalizacion_id')->nullable()->constrained('hospitalizaciones')->onDelete('cascade');
            $table->foreignId('consulta_id')->nullable()->constrained('consultas')->onDelete('cascade');
            $table->foreignId('recepcion_id')->constrained('users')->onDelete('cascade');
            $table->integer('atencion_medica')->nullable(); // 1-5
            $table->integer('atencion_enfermeria')->nullable(); // 1-5
            $table->integer('limpieza_habitacion')->nullable(); // 1-5
            $table->integer('comida')->nullable(); // 1-5
            $table->integer('personal_recepcion')->nullable(); // 1-5
            $table->integer('tiempo_espera')->nullable(); // 1-5
            $table->integer('calidad_general')->nullable(); // 1-5
            $table->text('comentarios')->nullable();
            $table->boolean('recomendaria')->nullable();
            $table->dateTime('fecha_encuesta');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encuestas_satisfaccion');
    }
};
