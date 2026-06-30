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
        Schema::create('grade_scale_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_scale_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('grade');
            $table->decimal('min_marks', 8, 2);
            $table->decimal('max_marks', 8, 2);
            $table->decimal('grade_point', 5, 2)->nullable();
            $table->boolean('is_passing')->default(true);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['grade_scale_id', 'grade']);
            $table->index(['grade_scale_id', 'min_marks', 'max_marks']);
        });

        DB::statement(<<<'SQL'
            alter table grade_scale_rules
            add constraint grade_scale_rules_marks_order_check
            check (min_marks <= max_marks)
        SQL);

        DB::statement(<<<'SQL'
            alter table grade_scale_rules
            add constraint grade_scale_rules_marks_non_negative_check
            check (min_marks >= 0 and max_marks >= 0)
        SQL);

        DB::statement(<<<'SQL'
            alter table grade_scale_rules
            add constraint grade_scale_rules_grade_point_non_negative_check
            check (grade_point is null or grade_point >= 0)
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_scale_rules');
    }
};
