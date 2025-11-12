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
            $table->text('observaciones_jefe_enfermeria')->nullable()->after('comentarios_paciente');
            $table->foreignId('revisado_por')->nullable()->constrained('users')->onDelete('set null')->after('observaciones_jefe_enfermeria');
            $table->datetime('fecha_revision')->nullable()->after('revisado_por');
            $table->string('dosis')->nullable()->after('fecha_revision');
            $table->string('frecuencia')->nullable()->after('dosis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tratamientos', function (Blueprint $table) {
            $table->dropForeign(['revisado_por']);
            $table->dropColumn([
                'observaciones_jefe_enfermeria',
                'revisado_por',
                'fecha_revision',
                'dosis',
                'frecuencia'
            ]);
        });
    }
};