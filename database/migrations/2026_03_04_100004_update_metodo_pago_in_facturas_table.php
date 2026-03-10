<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Expandir enum para incluir los 5 métodos de pago del sistema
        Schema::table('facturas', function (Blueprint $table) {
            $table->enum('metodo_pago', [
                'efectivo', 'tarjeta_debito', 'tarjeta_credito', 'transferencia', 'eps_convenio',
            ])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->enum('metodo_pago', [
                'efectivo', 'tarjeta', 'transferencia',
            ])->nullable()->change();
        });
    }
};
