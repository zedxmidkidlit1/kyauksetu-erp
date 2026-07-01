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
        Schema::create('announcement_audiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('audience_type')->default('all')->index();
            $table->string('role_name')->nullable()->index();
            $table->foreignId('department_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('program_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('major_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('class_section_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['announcement_id', 'audience_type']);
            $table->index(['audience_type', 'role_name']);
            $table->index(['audience_type', 'department_id']);
            $table->index(['audience_type', 'program_id']);
            $table->index(['audience_type', 'major_id']);
            $table->index(['audience_type', 'class_section_id']);
            $table->index(['audience_type', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_audiences');
    }
};
