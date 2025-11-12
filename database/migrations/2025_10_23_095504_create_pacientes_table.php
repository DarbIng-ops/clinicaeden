<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('dni', 20)->unique();
            $table->string('nombres');
            $table->string('apellidos');
            $table->date('fecha_nacimiento');
            $table->enum('sexo', ['M', 'F', 'Otro']);
            $table->string('telefono');
            $table->string('email')->nullable();
            $table->text('direccion');
            $table->string('ciudad')->default('Montevideo');
            $table->string('tipo_sangre')->nullable();
            $table->text('alergias')->nullable();
            $table->text('enfermedades_cronicas')->nullable();
            $table->string('contacto_emergencia_nombre');
            $table->string('contacto_emergencia_telefono');
            $table->string('foto')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};