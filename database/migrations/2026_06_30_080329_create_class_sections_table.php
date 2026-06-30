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
        Schema::create('class_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('major_id')->constrained()->cascadeOnUpdate();
            $table->string('name');
            $table->unsignedSmallInteger('year_level');
            $table->string('section');
            $table->string('status')->default('active')->index();
            $table->timestamps();

            $table->unique(['academic_year_id', 'major_id', 'year_level', 'section']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_sections');
    }
};
