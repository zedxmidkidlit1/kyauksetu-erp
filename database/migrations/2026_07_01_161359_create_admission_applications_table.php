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
        Schema::create('admission_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_batch_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('applicant_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('academic_year_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('program_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('major_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->string('application_no')->unique();
            $table->timestamp('applied_at')->nullable()->index();
            $table->string('application_status')->default('draft')->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(
                ['admission_batch_id', 'applicant_id', 'program_id'],
                'admission_applications_unique_batch_applicant_program',
            );
            $table->index(['admission_batch_id', 'application_status'], 'admission_applications_batch_status_index');
            $table->index(['applicant_id', 'application_status']);
            $table->index(['academic_year_id', 'application_status'], 'admission_applications_year_status_index');
            $table->index(['program_id', 'application_status']);
            $table->index(['major_id', 'application_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_applications');
    }
};
