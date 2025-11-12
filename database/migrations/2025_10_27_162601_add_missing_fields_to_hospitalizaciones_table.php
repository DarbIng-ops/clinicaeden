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
        Schema::table('hospitalizaciones', function (Blueprint $table) {
            $table->text('observaciones_alta_enfermeria')->nullable()->after('observaciones');
            $table->datetime('fecha_alta_enfermeria')->nullable()->after('observaciones_alta_enfermeria');
            $table->json('comentarios_auxiliares')->nullable()->after('fecha_alta_enfermeria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hospitalizaciones', function (Blueprint $table) {
            $table->dropColumn([
                'observaciones_alta_enfermeria',
                'fecha_alta_enfermeria',
                'comentarios_auxiliares'
            ]);
        });
    }
};