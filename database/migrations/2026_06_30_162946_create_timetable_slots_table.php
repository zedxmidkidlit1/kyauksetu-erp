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
        Schema::create('timetable_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timetable_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('teaching_assignment_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('teacher_profile_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('day_of_week')->index();
            $table->time('starts_at');
            $table->time('ends_at');
            $table->string('slot_type')->default('lecture')->index();
            $table->string('status')->default('scheduled')->index();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['timetable_id', 'day_of_week', 'starts_at']);
            $table->index(['teaching_assignment_id', 'day_of_week']);
            $table->index(['teacher_profile_id', 'day_of_week', 'starts_at']);
            $table->index(['room_id', 'day_of_week', 'starts_at']);
        });

        DB::statement(<<<'SQL'
            alter table timetable_slots
            add constraint timetable_slots_time_order_check
            check (starts_at < ends_at)
        SQL);

        DB::statement(<<<'SQL'
            create unique index timetable_slots_unique_exact_slot
            on timetable_slots (
                timetable_id,
                coalesce(teaching_assignment_id, 0),
                coalesce(course_id, 0),
                coalesce(teacher_profile_id, 0),
                coalesce(room_id, 0),
                day_of_week,
                starts_at,
                ends_at,
                slot_type
            )
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_slots');
    }
};
