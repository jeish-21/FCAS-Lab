<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('faculty_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('reason');
            $table->text('description')->nullable();
            $table->string('consultation_type'); // e.g., 'Virtual Video', 'In-Person', 'Phone Call'
            $table->date('appointment_date');
            $table->string('time_slot');
            $table->string('status')->default('pending'); // pending, approved, rejected, completed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};