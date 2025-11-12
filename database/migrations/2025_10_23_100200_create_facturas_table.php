<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_factura', 20)->unique();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('hospitalizacion_id')->nullable()->constrained('hospitalizaciones')->onDelete('cascade');
            $table->foreignId('consulta_id')->nullable()->constrained('consultas')->onDelete('cascade');
            $table->foreignId('caja_id')->constrained('users')->onDelete('cascade');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('impuestos', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia']);
            $table->enum('estado', ['pendiente', 'pagado', 'cancelado']);
            $table->dateTime('fecha_emision');
            $table->dateTime('fecha_pago')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
