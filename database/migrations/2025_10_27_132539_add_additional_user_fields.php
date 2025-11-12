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
        Schema::table('users', function (Blueprint $table) {
            $table->string('apellido')->nullable()->after('name');
            $table->string('dni', 20)->unique()->nullable()->after('apellido');
            $table->string('diploma_path')->nullable()->after('dni');
            $table->string('direccion')->nullable()->after('diploma_path');
            $table->date('fecha_nacimiento')->nullable()->after('direccion');
            $table->enum('sexo', ['M', 'F', 'O'])->nullable()->after('fecha_nacimiento');
            $table->text('observaciones')->nullable()->after('sexo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'apellido',
                'dni',
                'diploma_path',
                'direccion',
                'fecha_nacimiento',
                'sexo',
                'observaciones'
            ]);
        });
    }
};