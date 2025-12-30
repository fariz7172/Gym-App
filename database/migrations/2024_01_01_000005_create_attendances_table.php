<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->dateTime('check_in');
            $table->dateTime('check_out')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'check_in']);
            $table->index(['branch_id', 'check_in']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
