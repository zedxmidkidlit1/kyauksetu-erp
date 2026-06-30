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
        Schema::create('student_course_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_enrollment_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('grade_scale_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->decimal('total_marks', 8, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('grade')->nullable();
            $table->decimal('grade_point', 5, 2)->nullable();
            $table->string('result_status')->default('draft')->index();
            $table->foreignId('calculated_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamp('calculated_at')->nullable()->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['student_enrollment_id', 'academic_year_id']);
            $table->index(['academic_year_id', 'semester_id']);
            $table->index(['course_id', 'result_status']);
            $table->index(['grade_scale_id', 'grade']);
        });

        DB::statement(<<<'SQL'
            create unique index student_course_results_unique_course_result
            on student_course_results (
                student_enrollment_id,
                course_id,
                academic_year_id,
                coalesce(semester_id, 0)
            )
        SQL);

        DB::statement(<<<'SQL'
            alter table student_course_results
            add constraint student_course_results_total_marks_non_negative_check
            check (total_marks is null or total_marks >= 0)
        SQL);

        DB::statement(<<<'SQL'
            alter table student_course_results
            add constraint student_course_results_percentage_non_negative_check
            check (percentage is null or percentage >= 0)
        SQL);

        DB::statement(<<<'SQL'
            alter table student_course_results
            add constraint student_course_results_grade_point_non_negative_check
            check (grade_point is null or grade_point >= 0)
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_course_results');
    }
};
