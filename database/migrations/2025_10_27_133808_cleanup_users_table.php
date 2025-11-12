<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Limpiar tabla users eliminando columnas redundantes y corrigiendo estructura
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar foreign key primero
            $table->dropForeign(['jefe_enfermeria_id']);
            // Luego eliminar columnas redundantes
            $table->dropColumn([
                'foto',                    // Redundante con profile_photo_path
                'jefe_enfermeria_id',      // No pertenece aquí, está en modulos_enfermeria
                'disponible'               // Redundante con activo
            ]);
        });
    }

    /**
     * Revertir cambios
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('role');
            $table->foreignId('jefe_enfermeria_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('disponible')->default(true);
        });
    }
};