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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('movement_type')->index();
            $table->unsignedInteger('quantity');
            $table->date('movement_date')->index();
            $table->string('reference')->nullable();
            $table->foreignId('handled_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['stock_item_id', 'movement_type']);
            $table->index(['stock_item_id', 'movement_date']);
            $table->index('reference');
        });

        DB::statement(
            'alter table stock_movements add constraint stock_movements_quantity_non_negative check (quantity >= 0)',
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
