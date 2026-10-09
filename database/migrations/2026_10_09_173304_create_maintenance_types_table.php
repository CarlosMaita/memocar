<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('category');
            // Intervalo recomendado (referencia común) y seteado (el que usa la app para alertar).
            // null = sin intervalo por ese criterio.
            $table->unsignedInteger('recommended_km')->nullable();
            $table->unsignedSmallInteger('recommended_months')->nullable();
            $table->unsignedInteger('set_km')->nullable();
            $table->unsignedSmallInteger('set_months')->nullable();
            $table->string('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_types');
    }
};
