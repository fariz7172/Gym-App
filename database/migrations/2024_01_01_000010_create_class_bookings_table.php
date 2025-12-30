<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->date('booking_date');
            $table->enum('status', ['booked', 'attended', 'cancelled', 'no_show'])->default('booked');
            $table->timestamps();

            $table->unique(['class_schedule_id', 'member_id', 'booking_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_bookings');
    }
};
