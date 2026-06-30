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
        Schema::create('curricula', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('major_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('year_level')->index();
            $table->string('status')->default('active')->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['program_id', 'major_id', 'year_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curricula');
    }
};
