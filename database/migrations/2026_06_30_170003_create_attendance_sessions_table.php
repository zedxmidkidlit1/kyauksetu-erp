<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('class_section_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('teaching_assignment_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('timetable_slot_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('teacher_profile_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->date('session_date')->index();
            $table->time('starts_at')->nullable();
            $table->time('ends_at')->nullable();
            $table->string('status')->default('draft')->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['class_section_id', 'session_date']);
            $table->index(['course_id', 'session_date']);
            $table->index(['teacher_profile_id', 'session_date']);
            $table->index(['teaching_assignment_id', 'session_date']);
            $table->index(['timetable_slot_id', 'session_date']);
        });

        DB::statement(<<<'SQL'
            alter table attendance_sessions
            add constraint attendance_sessions_time_order_check
            check (starts_at is null or ends_at is null or starts_at < ends_at)
        SQL);

        DB::statement(<<<'SQL'
            create unique index attendance_sessions_unique_exact_session
            on attendance_sessions (
                academic_year_id,
                coalesce(semester_id, 0),
                class_section_id,
                coalesce(teaching_assignment_id, 0),
                coalesce(timetable_slot_id, 0),
                course_id,
                coalesce(teacher_profile_id, 0),
                coalesce(room_id, 0),
                session_date,
                coalesce(starts_at, time '00:00:00'),
                coalesce(ends_at, time '00:00:00')
            )
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_sessions');
    }
};
