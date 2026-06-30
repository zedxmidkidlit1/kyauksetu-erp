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
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_term_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('class_section_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('teaching_assignment_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('teacher_profile_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->date('exam_date')->index();
            $table->time('starts_at');
            $table->time('ends_at');
            $table->string('status')->default('draft')->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['exam_term_id', 'exam_date']);
            $table->index(['class_section_id', 'exam_date']);
            $table->index(['course_id', 'exam_date']);
            $table->index(['teacher_profile_id', 'exam_date']);
            $table->index(['room_id', 'exam_date']);
        });

        DB::statement(<<<'SQL'
            alter table exam_schedules
            add constraint exam_schedules_time_order_check
            check (starts_at < ends_at)
        SQL);

        DB::statement(<<<'SQL'
            create unique index exam_schedules_unique_exact_schedule
            on exam_schedules (
                exam_term_id,
                class_section_id,
                course_id,
                exam_date,
                starts_at,
                ends_at
            )
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_schedules');
    }
};
