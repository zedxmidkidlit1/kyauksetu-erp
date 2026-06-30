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
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->renameColumn('student_number', 'student_no');
        });

        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->renameColumn('employee_number', 'staff_no');
            $table->renameColumn('title', 'rank');
        });

        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->renameColumn('employee_number', 'staff_no');
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('student_no')->nullable()->change();
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
            $table->string('roll_no')->nullable()->unique();
            $table->string('institutional_email')->nullable()->unique();
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('major_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('class_section_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('admission_year')->nullable()->index();
        });

        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->string('staff_no')->nullable()->change();
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
            $table->string('institutional_email')->nullable()->unique();
            $table->string('position')->nullable();
            $table->string('rank')->nullable()->change();
        });

        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->string('staff_no')->nullable()->change();
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
            $table->string('position')->nullable()->change();
            $table->string('institutional_email')->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->dropForeign(['major_id']);
            $table->dropForeign(['academic_year_id']);
            $table->dropForeign(['class_section_id']);
            $table->dropUnique(['roll_no']);
            $table->dropUnique(['institutional_email']);
            $table->dropIndex(['admission_year']);
            $table->dropColumn([
                'roll_no',
                'institutional_email',
                'program_id',
                'major_id',
                'academic_year_id',
                'class_section_id',
                'admission_year',
            ]);
            $table->string('student_no')->nullable(false)->change();
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
        });

        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->dropUnique(['institutional_email']);
            $table->dropColumn(['institutional_email', 'position']);
            $table->string('staff_no')->nullable(false)->change();
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
            $table->string('rank')->nullable()->change();
        });

        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->dropUnique(['institutional_email']);
            $table->dropColumn('institutional_email');
            $table->string('staff_no')->nullable(false)->change();
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
            $table->string('position')->nullable(false)->change();
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->renameColumn('student_no', 'student_number');
        });

        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->renameColumn('staff_no', 'employee_number');
            $table->renameColumn('rank', 'title');
        });

        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->renameColumn('staff_no', 'employee_number');
        });
    }
};
