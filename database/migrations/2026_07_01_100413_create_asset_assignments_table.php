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
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('assigned_to_user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('assigned_to_department_id')
                ->nullable()
                ->constrained('departments')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('assigned_to_room_id')
                ->nullable()
                ->constrained('rooms')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->timestamp('assigned_at');
            $table->timestamp('returned_at')->nullable();
            $table->string('assignment_status')->default('active')->index();
            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('returned_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['asset_id', 'assignment_status']);
            $table->index(['assigned_to_user_id', 'assignment_status']);
            $table->index(['assigned_to_department_id', 'assignment_status']);
            $table->index(['assigned_to_room_id', 'assignment_status']);
            $table->index('assigned_at');
            $table->index('returned_at');
        });

        DB::statement(
            'alter table asset_assignments add constraint asset_assignments_returned_after_assigned check (returned_at is null or returned_at >= assigned_at)',
        );
        DB::statement(
            "create unique index asset_assignments_one_active_per_asset on asset_assignments (asset_id) where assignment_status = 'active'",
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
    }
};
