<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->decimal('descuento_porcentaje', 5, 2)->default(0)->after('total');
            $table->decimal('descuento_monto', 10, 2)->default(0)->after('descuento_porcentaje');
            $table->text('motivo_descuento')->nullable()->after('descuento_monto');
        });
    }

    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn(['descuento_porcentaje', 'descuento_monto', 'motivo_descuento']);
        });
    }
};
