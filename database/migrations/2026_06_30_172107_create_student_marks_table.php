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
        Schema::create('student_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_component_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('student_enrollment_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('marks_obtained', 8, 2)->nullable();
            $table->string('status')->default('draft')->index();
            $table->foreignId('entered_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamp('entered_at')->nullable()->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['assessment_component_id', 'student_enrollment_id']);
            $table->index(['student_enrollment_id', 'status']);
        });

        DB::statement(<<<'SQL'
            alter table student_marks
            add constraint student_marks_obtained_non_negative_check
            check (marks_obtained is null or marks_obtained >= 0)
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_marks');
    }
};
