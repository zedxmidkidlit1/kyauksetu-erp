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
        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('student_enrollment_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('academic_year_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('semester_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('fee_type_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->decimal('discount_amount', 12, 2)->nullable();
            $table->decimal('payable_amount', 12, 2);
            $table->date('due_at')->nullable();
            $table->string('fee_status')->default('pending')->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['student_profile_id', 'fee_status']);
            $table->index(['student_enrollment_id', 'fee_status']);
            $table->index(['academic_year_id', 'semester_id']);
            $table->index(['fee_type_id', 'fee_status']);
            $table->index('due_at');
        });

        DB::statement('alter table student_fees add constraint student_fees_amount_non_negative check (amount >= 0)');
        DB::statement('alter table student_fees add constraint student_fees_discount_amount_non_negative check (discount_amount is null or discount_amount >= 0)');
        DB::statement('alter table student_fees add constraint student_fees_payable_amount_non_negative check (payable_amount >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_fees');
    }
};
