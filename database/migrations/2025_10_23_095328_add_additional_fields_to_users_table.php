<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('role');
            $table->string('especialidad')->nullable()->after('foto');
            $table->string('numero_licencia')->nullable()->after('especialidad');
            $table->string('telefono')->nullable()->after('numero_licencia');
            $table->boolean('activo')->default(true)->after('telefono');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['foto', 'especialidad', 'numero_licencia', 'telefono', 'activo']);
        });
    }
};