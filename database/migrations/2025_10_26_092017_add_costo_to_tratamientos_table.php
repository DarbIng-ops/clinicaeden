<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tratamientos', function (Blueprint $table) {
            $table->decimal('costo', 10, 2)->default(0)->after('indicaciones');
            $table->foreignId('hospitalizacion_id')->nullable()->constrained('hospitalizaciones')->onDelete('cascade')->after('consulta_id');
        });
    }

    public function down(): void
    {
        Schema::table('tratamientos', function (Blueprint $table) {
            $table->dropForeign(['hospitalizacion_id']);
            $table->dropColumn(['costo', 'hospitalizacion_id']);
        });
    }
};