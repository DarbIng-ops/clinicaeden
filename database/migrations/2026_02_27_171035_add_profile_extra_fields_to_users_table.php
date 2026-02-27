<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('tipo_sangre', 5)->nullable()->after('telefono');
            $table->string('contacto_emergencia_nombre')->nullable()->after('tipo_sangre');
            $table->string('contacto_emergencia_telefono', 30)->nullable()->after('contacto_emergencia_nombre');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['tipo_sangre', 'contacto_emergencia_nombre', 'contacto_emergencia_telefono']);
        });
    }
};
