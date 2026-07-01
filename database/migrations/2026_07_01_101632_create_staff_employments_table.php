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
        Schema::create('staff_employments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('staff_profile_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('teacher_profile_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('department_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('staff_position_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->string('employment_type')->index();
            $table->string('employment_status')->default('active')->index();
            $table->date('joined_at')->nullable()->index();
            $table->date('ended_at')->nullable()->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'employment_status']);
            $table->index(['staff_profile_id', 'employment_status']);
            $table->index(['teacher_profile_id', 'employment_status']);
            $table->index(['department_id', 'employment_status']);
            $table->index(['staff_position_id', 'employment_status']);
        });

        DB::statement(
            'alter table staff_employments add constraint staff_employments_ended_after_joined check (ended_at is null or joined_at is null or ended_at >= joined_at)',
        );
        DB::statement(
            "create unique index staff_employments_one_active_per_user on staff_employments (user_id) where employment_status = 'active'",
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_employments');
    }
};
