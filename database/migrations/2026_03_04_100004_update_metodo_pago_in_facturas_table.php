<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Expandir enum para incluir los 5 métodos de pago del sistema
        DB::statement("ALTER TABLE facturas MODIFY COLUMN metodo_pago
            ENUM('efectivo','tarjeta_debito','tarjeta_credito','transferencia','eps_convenio') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE facturas MODIFY COLUMN metodo_pago
            ENUM('efectivo','tarjeta','transferencia') NULL");
    }
};
