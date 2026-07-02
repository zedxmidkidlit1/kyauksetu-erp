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
        Schema::create('kai_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kai_chat_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role');
            $table->text('content');
            $table->json('context_keys')->nullable();
            $table->string('driver')->nullable();
            $table->string('provider')->nullable();
            $table->string('model')->nullable();
            $table->string('status')->default('completed');
            $table->string('error_code')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['kai_chat_session_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['role', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kai_chat_messages');
    }
};
