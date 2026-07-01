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
        Schema::create('book_copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('accession_no')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('copy_status')->default('available')->index();
            $table->string('shelf_location')->nullable();
            $table->date('acquired_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['book_id', 'copy_status']);
            $table->index('shelf_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_copies');
    }
};
