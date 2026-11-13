<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cor_path')->nullable();
        });

        Schema::table('faculty_schedules', function (Blueprint $table) {
            $table->date('schedule_date')->nullable()->after('faculty_id');
            $table->index(['faculty_id', 'schedule_date', 'time_slot']);
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');

        Schema::table('faculty_schedules', function (Blueprint $table) {
            $table->dropIndex(['faculty_id', 'schedule_date', 'time_slot']);
            $table->dropColumn('schedule_date');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('cor_path');
        });
    }
};
