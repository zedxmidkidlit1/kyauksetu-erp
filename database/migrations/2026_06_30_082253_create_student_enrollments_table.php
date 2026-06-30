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
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('program_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('major_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('class_section_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedSmallInteger('year_level')->nullable()->index();
            $table->string('roll_no')->nullable()->index();
            $table->string('status')->default('active')->index();
            $table->date('enrolled_at')->nullable()->index();
            $table->date('completed_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['student_profile_id', 'academic_year_id', 'semester_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};
