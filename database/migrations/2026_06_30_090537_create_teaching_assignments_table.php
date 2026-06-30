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
        Schema::create('teaching_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_profile_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('program_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('major_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('class_section_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('curriculum_id')->nullable()->constrained('curricula')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('curriculum_course_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('status')->default('active')->index();
            $table->date('starts_at')->nullable()->index();
            $table->date('ends_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['teacher_profile_id', 'academic_year_id', 'semester_id']);
            $table->index(['course_id', 'academic_year_id', 'semester_id']);
            $table->index(['class_section_id', 'academic_year_id', 'semester_id']);
        });

        DB::statement(<<<'SQL'
            create unique index teaching_assignments_unique_assignment
            on teaching_assignments (
                teacher_profile_id,
                course_id,
                academic_year_id,
                coalesce(semester_id, 0),
                coalesce(class_section_id, 0)
            )
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_assignments');
    }
};
