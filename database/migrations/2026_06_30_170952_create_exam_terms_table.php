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
        Schema::create('exam_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name');
            $table->string('exam_type')->index();
            $table->date('starts_at')->nullable()->index();
            $table->date('ends_at')->nullable();
            $table->string('status')->default('draft')->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['academic_year_id', 'semester_id']);
        });

        DB::statement(<<<'SQL'
            alter table exam_terms
            add constraint exam_terms_date_order_check
            check (starts_at is null or ends_at is null or starts_at <= ends_at)
        SQL);

        DB::statement(<<<'SQL'
            create unique index exam_terms_unique_name
            on exam_terms (
                academic_year_id,
                coalesce(semester_id, 0),
                name,
                exam_type
            )
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_terms');
    }
};
