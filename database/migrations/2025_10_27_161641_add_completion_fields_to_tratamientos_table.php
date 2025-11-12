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
        Schema::table('tratamientos', function (Blueprint $table) {
            $table->datetime('fecha_completado')->nullable()->after('fecha_programada');
            $table->text('observaciones_procedimiento')->nullable()->after('fecha_completado');
            $table->time('hora_aplicacion')->nullable()->after('observaciones_procedimiento');
            $table->foreignId('completado_por')->nullable()->constrained('users')->onDelete('set null')->after('hora_aplicacion');
            $table->text('comentarios_paciente')->nullable()->after('completado_por');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tratamientos', function (Blueprint $table) {
            $table->dropForeign(['completado_por']);
            $table->dropColumn([
                'fecha_completado',
                'observaciones_procedimiento', 
                'hora_aplicacion',
                'completado_por',
                'comentarios_paciente'
            ]);
        });
    }
};