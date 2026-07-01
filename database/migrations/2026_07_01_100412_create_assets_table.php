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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_category_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('asset_tag')->unique();
            $table->string('name');
            $table->string('serial_number')->nullable()->unique();
            $table->text('description')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 12, 2)->nullable();
            $table->string('asset_status')->default('available')->index();
            $table->foreignId('building_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('room_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('department_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('name');
            $table->index(['asset_category_id', 'asset_status']);
            $table->index(['department_id', 'asset_status']);
            $table->index(['building_id', 'room_id']);
            $table->index('purchase_date');
        });

        DB::statement(
            'alter table assets add constraint assets_purchase_cost_non_negative check (purchase_cost is null or purchase_cost >= 0)',
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
