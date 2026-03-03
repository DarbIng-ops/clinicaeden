<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            // Una factura pendiente aún no tiene método de pago; se asigna cuando el cajero procesa el cobro.
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia'])->nullable(false)->change();
        });
    }
};
