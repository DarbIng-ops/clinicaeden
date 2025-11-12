<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('jefe_enfermeria_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('disponible')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['jefe_enfermeria_id']);
            $table->dropColumn(['jefe_enfermeria_id', 'disponible']);
        });
    }
};