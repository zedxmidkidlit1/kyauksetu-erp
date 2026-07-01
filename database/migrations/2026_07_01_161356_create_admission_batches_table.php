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
        Schema::create('admission_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('program_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->text('description')->nullable();
            $table->date('opens_at')->nullable()->index();
            $table->date('closes_at')->nullable()->index();
            $table->unsignedInteger('capacity')->nullable();
            $table->string('status')->default('draft')->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('name');
            $table->index(['academic_year_id', 'status']);
            $table->index(['program_id', 'status']);
        });

        DB::statement(
            'alter table admission_batches add constraint admission_batches_closes_after_opens check (closes_at is null or opens_at is null or closes_at >= opens_at)',
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_batches');
    }
};
