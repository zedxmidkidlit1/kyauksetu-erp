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
        Schema::create('staff_leave_requests', function (Blueprint $table) {
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
            $table->string('leave_type')->index();
            $table->date('starts_at')->index();
            $table->date('ends_at')->index();
            $table->string('status')->default('draft')->index();
            $table->timestamp('requested_at')->nullable()->index();
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['staff_profile_id', 'status']);
            $table->index(['teacher_profile_id', 'status']);
        });

        DB::statement(
            'alter table staff_leave_requests add constraint staff_leave_requests_starts_before_ends check (starts_at <= ends_at)',
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_leave_requests');
    }
};
