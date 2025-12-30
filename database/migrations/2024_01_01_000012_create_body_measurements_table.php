<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('body_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->date('measured_at');
            $table->decimal('weight', 5, 2)->nullable(); // kg
            $table->decimal('height', 5, 2)->nullable(); // cm
            $table->decimal('body_fat_percentage', 4, 2)->nullable();
            $table->decimal('chest', 5, 2)->nullable(); // cm
            $table->decimal('waist', 5, 2)->nullable(); // cm
            $table->decimal('hips', 5, 2)->nullable(); // cm
            $table->decimal('arms', 5, 2)->nullable(); // cm
            $table->decimal('thighs', 5, 2)->nullable(); // cm
            $table->text('notes')->nullable();
            $table->string('photo_front')->nullable();
            $table->string('photo_side')->nullable();
            $table->string('photo_back')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'measured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('body_measurements');
    }
};
