<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones_sistema', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_emisor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('usuario_receptor_id')->constrained('users')->onDelete('cascade');
            $table->string('tipo'); // 'nuevo_paciente', 'tratamiento_asignado', 'pago_recibido', etc.
            $table->string('titulo');
            $table->text('mensaje');
            $table->json('datos_adicionales')->nullable();
            $table->boolean('leida')->default(false);
            $table->dateTime('fecha_leida')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones_sistema');
    }
};
