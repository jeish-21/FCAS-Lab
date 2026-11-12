<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faculty_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('faculty_id');
            $table->string('day_of_week'); // Mon, Tue, Wed, Thu, Fri
            $table->string('time_slot');   // 08:00 AM, 09:00 AM, etc.
            $table->string('slot_type')->default('Consultation'); // Consultation, Lecture, Break
            $table->string('title')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faculty_schedules');
    }
};