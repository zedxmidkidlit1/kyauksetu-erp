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
        Schema::create('staff_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('staff_profile_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('teacher_profile_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->string('document_type')->index();
            $table->string('title');
            $table->string('file_path')->nullable();
            $table->date('issued_at')->nullable()->index();
            $table->date('expires_at')->nullable()->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('title');
            $table->index(['user_id', 'document_type']);
            $table->index(['staff_profile_id', 'document_type']);
            $table->index(['teacher_profile_id', 'document_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_documents');
    }
};
