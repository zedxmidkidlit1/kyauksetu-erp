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
        Schema::create('student_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_fee_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('student_profile_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->nullable()->index();
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at');
            $table->string('payment_status')->default('pending')->index();
            $table->foreignId('received_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['student_fee_id', 'payment_status']);
            $table->index(['student_profile_id', 'payment_status']);
            $table->index('paid_at');
            $table->index('payment_reference');
        });

        DB::statement('alter table student_payments add constraint student_payments_amount_non_negative check (amount >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_payments');
    }
};
