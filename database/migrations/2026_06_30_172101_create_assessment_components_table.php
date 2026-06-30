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
        Schema::create('assessment_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('class_section_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('exam_term_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('exam_schedule_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name');
            $table->string('component_type')->default('assignment')->index();
            $table->decimal('max_marks', 8, 2);
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('status')->default('draft')->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['academic_year_id', 'semester_id']);
            $table->index(['class_section_id', 'course_id']);
            $table->index(['exam_term_id', 'exam_schedule_id']);
        });

        DB::statement(<<<'SQL'
            alter table assessment_components
            add constraint assessment_components_max_marks_positive_check
            check (max_marks > 0)
        SQL);

        DB::statement(<<<'SQL'
            alter table assessment_components
            add constraint assessment_components_weight_non_negative_check
            check (weight is null or weight >= 0)
        SQL);

        DB::statement(<<<'SQL'
            create unique index assessment_components_unique_exact_component
            on assessment_components (
                academic_year_id,
                coalesce(semester_id, 0),
                coalesce(class_section_id, 0),
                course_id,
                coalesce(exam_term_id, 0),
                coalesce(exam_schedule_id, 0),
                name,
                component_type
            )
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_components');
    }
};
