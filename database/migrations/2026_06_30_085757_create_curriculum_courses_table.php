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
        Schema::create('curriculum_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->boolean('is_required')->default(true)->index();
            $table->unsignedSmallInteger('sort_order')->nullable()->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['curriculum_id', 'course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculum_courses');
    }
};
