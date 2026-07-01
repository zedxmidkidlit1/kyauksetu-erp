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
        Schema::create('hostel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('name');
            $table->string('room_no');
            $table->string('floor')->nullable();
            $table->smallInteger('capacity')->nullable();
            $table->string('room_type')->nullable()->index();
            $table->string('status')->default('active')->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['hostel_id', 'room_no']);
            $table->index(['hostel_id', 'status']);
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_rooms');
    }
};
