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
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('program_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('major_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('class_section_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name');
            $table->date('effective_from')->nullable()->index();
            $table->date('effective_until')->nullable();
            $table->string('status')->default('active')->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['class_section_id', 'academic_year_id', 'semester_id']);
            $table->index(['program_id', 'major_id']);
        });

        DB::statement(<<<'SQL'
            create unique index timetables_unique_section_name
            on timetables (
                class_section_id,
                academic_year_id,
                coalesce(semester_id, 0),
                name
            )
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
