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
        Schema::table('consultas', function (Blueprint $table) {
            $table->string('presion_arterial')->nullable()->after('estado');
            $table->decimal('temperatura', 4, 2)->nullable()->after('presion_arterial');
            $table->integer('frecuencia_cardiaca')->nullable()->after('temperatura');
            $table->integer('frecuencia_respiratoria')->nullable()->after('frecuencia_cardiaca');
            $table->integer('saturacion_oxigeno')->nullable()->after('frecuencia_respiratoria');
            $table->decimal('peso', 5, 2)->nullable()->after('saturacion_oxigeno');
            $table->decimal('talla', 3, 2)->nullable()->after('peso');
            $table->datetime('hora_atencion')->nullable()->after('talla');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->dropColumn([
                'presion_arterial',
                'temperatura',
                'frecuencia_cardiaca',
                'frecuencia_respiratoria',
                'saturacion_oxigeno',
                'peso',
                'talla',
                'hora_atencion'
            ]);
        });
    }
};
