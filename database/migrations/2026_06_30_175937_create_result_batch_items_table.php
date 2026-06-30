<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('result_batch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('result_batch_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('student_course_result_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('student_enrollment_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('status')->default('included')->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['result_batch_id', 'student_course_result_id']);
            $table->unique(['result_batch_id', 'student_enrollment_id', 'course_id']);
            $table->index(['student_enrollment_id', 'course_id']);
            $table->index(['course_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('result_batch_items');
    }
};
