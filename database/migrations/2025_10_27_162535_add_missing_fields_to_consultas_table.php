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
            $table->string('motivo_consulta')->nullable()->after('motivo');
            $table->string('hora_consulta')->nullable()->after('fecha_consulta');
            $table->text('tratamiento')->nullable()->after('diagnostico');
            $table->enum('tipo_consulta', ['general', 'especializada'])->default('general')->after('tratamiento');
            $table->string('especialidad')->nullable()->after('tipo_consulta');
            $table->enum('estado', ['pendiente', 'completada', 'cancelada'])->default('pendiente')->after('especialidad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->dropColumn([
                'motivo_consulta',
                'hora_consulta', 
                'tratamiento',
                'tipo_consulta',
                'especialidad',
                'estado'
            ]);
        });
    }
};