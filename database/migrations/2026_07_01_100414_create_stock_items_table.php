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
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('unit')->nullable();
            $table->unsignedInteger('quantity_on_hand')->default(0);
            $table->unsignedInteger('reorder_level')->nullable();
            $table->string('status')->default('active')->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('name');
            $table->index('unit');
        });

        DB::statement(
            'alter table stock_items add constraint stock_items_quantity_on_hand_non_negative check (quantity_on_hand >= 0)',
        );
        DB::statement(
            'alter table stock_items add constraint stock_items_reorder_level_non_negative check (reorder_level is null or reorder_level >= 0)',
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};
