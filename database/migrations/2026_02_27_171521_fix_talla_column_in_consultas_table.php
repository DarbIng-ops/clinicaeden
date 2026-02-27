<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            // decimal(3,2) sólo admite hasta 9.99 — insuficiente para talla en cm (ej. 171.5)
            // decimal(5,2) admite hasta 999.99, correcto para talla en centímetros
            $table->decimal('talla', 5, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->decimal('talla', 3, 2)->nullable()->change();
        });
    }
};
