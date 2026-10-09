<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->foreignId('maintenance_type_id')->constrained()->restrictOnDelete();
            $table->date('date');
            $table->unsignedInteger('km')->nullable();
            $table->text('description')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->timestamps();

            $table->index(['car_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
