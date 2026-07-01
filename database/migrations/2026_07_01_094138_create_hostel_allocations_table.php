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
        Schema::create('hostel_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('hostel_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('hostel_room_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('hostel_bed_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->timestamp('allocated_at');
            $table->timestamp('vacated_at')->nullable();
            $table->string('allocation_status')->default('active')->index();
            $table->foreignId('allocated_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('vacated_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['student_profile_id', 'allocation_status']);
            $table->index(['hostel_id', 'allocation_status']);
            $table->index(['hostel_room_id', 'allocation_status']);
            $table->index(['hostel_bed_id', 'allocation_status']);
            $table->index('allocated_at');
            $table->index('vacated_at');
        });

        DB::statement(
            "create unique index hostel_allocations_active_student_unique on hostel_allocations (student_profile_id) where allocation_status = 'active'",
        );
        DB::statement(
            "create unique index hostel_allocations_active_bed_unique on hostel_allocations (hostel_bed_id) where hostel_bed_id is not null and allocation_status = 'active'",
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_allocations');
    }
};
