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
        Schema::create('admission_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_application_id')
                ->unique()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('decision_status')->default('pending')->index();
            $table->foreignId('decided_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->timestamp('decided_at')->nullable()->index();
            $table->timestamp('offer_expires_at')->nullable()->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['decided_by', 'decision_status']);
        });

        DB::statement(
            'alter table admission_decisions add constraint admission_decisions_offer_expires_after_decided check (offer_expires_at is null or decided_at is null or offer_expires_at >= decided_at)',
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_decisions');
    }
};
