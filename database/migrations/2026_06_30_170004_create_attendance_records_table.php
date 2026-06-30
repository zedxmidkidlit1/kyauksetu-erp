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
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_session_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('student_enrollment_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('status')->index();
            $table->text('remarks')->nullable();
            $table->timestamp('marked_at')->nullable()->index();
            $table->foreignId('marked_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();

            $table->unique(['attendance_session_id', 'student_enrollment_id']);
            $table->index(['student_enrollment_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
