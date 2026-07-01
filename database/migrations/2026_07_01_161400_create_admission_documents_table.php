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
        Schema::create('admission_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_application_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('applicant_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('document_type')->index();
            $table->string('title');
            $table->string('file_path')->nullable();
            $table->date('issued_at')->nullable()->index();
            $table->date('expires_at')->nullable()->index();
            $table->string('document_status')->default('pending')->index();
            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->timestamp('verified_at')->nullable()->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('title');
            $table->index(['admission_application_id', 'document_type'], 'admission_documents_application_type_index');
            $table->index(['applicant_id', 'document_type']);
            $table->index(['verified_by', 'document_status']);
        });

        DB::statement(
            'alter table admission_documents add constraint admission_documents_expires_after_issued check (expires_at is null or issued_at is null or expires_at >= issued_at)',
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_documents');
    }
};
