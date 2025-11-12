<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historias_clinicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->unique()->constrained('pacientes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historias_clinicas');
    }
};