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
        Schema::table('admission_applications', function (Blueprint $table) {
            $table->foreignId('student_profile_id')
                ->nullable()
                ->unique()
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('converted_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->timestamp('converted_at')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_applications', function (Blueprint $table) {
            $table->dropForeign(['student_profile_id']);
            $table->dropUnique(['student_profile_id']);
            $table->dropForeign(['converted_by']);
            $table->dropColumn(['student_profile_id', 'converted_by', 'converted_at']);
        });
    }
};
