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
        // Eliminar tabla de habitaciones antigua si existe
        Schema::dropIfExists('habitaciones');
        
        // Crear tabla de pisos
        Schema::create('pisos', function (Blueprint $table) {
            $table->id();
            $table->integer('numero')->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Crear tabla de consultorios
        Schema::create('consultorios', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 10)->unique();
            $table->foreignId('piso_id')->constrained('pisos')->onDelete('cascade');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->boolean('disponible')->default(true);
            $table->timestamps();
        });

        // Crear tabla de módulos de enfermería
        Schema::create('modulos_enfermeria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('piso_id')->constrained('pisos')->onDelete('cascade');
            $table->string('nombre');
            $table->enum('tipo', ['general', 'partos_neonatos', 'hospitalizacion_general']);
            $table->text('descripcion')->nullable();
            $table->foreignId('jefe_enfermeria_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Crear tabla de equipos de enfermería (relación auxiliares-módulos)
        Schema::create('equipos_enfermeria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modulo_id')->constrained('modulos_enfermeria')->onDelete('cascade');
            $table->foreignId('auxiliar_enfermeria_id')->constrained('users')->onDelete('cascade');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            // Evitar duplicados de auxiliar en el mismo módulo
            $table->unique(['modulo_id', 'auxiliar_enfermeria_id']);
        });

        // Crear tabla de salas de procedimientos
        Schema::create('salas_procedimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modulo_id')->constrained('modulos_enfermeria')->onDelete('cascade');
            $table->string('numero', 10);
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->boolean('disponible')->default(true);
            $table->timestamps();
            
            // Evitar duplicados de número de sala en el mismo módulo
            $table->unique(['modulo_id', 'numero']);
        });

        // Crear tabla de habitaciones
        Schema::create('habitaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 10)->unique();
            $table->foreignId('modulo_id')->constrained('modulos_enfermeria')->onDelete('cascade');
            $table->integer('capacidad'); // número de camas o cunas
            $table->enum('tipo', ['general', 'partos', 'neonatos', 'madres_pre_post_parto']);
            $table->text('descripcion')->nullable();
            $table->boolean('disponible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar en orden inverso para respetar las foreign keys
        Schema::dropIfExists('habitaciones');
        Schema::dropIfExists('salas_procedimientos');
        Schema::dropIfExists('equipos_enfermeria');
        Schema::dropIfExists('modulos_enfermeria');
        Schema::dropIfExists('consultorios');
        Schema::dropIfExists('pisos');
    }
};
