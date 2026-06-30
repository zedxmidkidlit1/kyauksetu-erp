<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class IamRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'departments.view',
            'departments.create',
            'departments.update',
            'departments.delete',
            'students.view',
            'students.create',
            'students.update',
            'teachers.view',
            'teachers.create',
            'teachers.update',
            'kai.chat',
            'academic_years.view',
            'academic_years.create',
            'academic_years.update',
            'academic_years.delete',
            'semesters.view',
            'semesters.create',
            'semesters.update',
            'semesters.delete',
            'programs.view',
            'programs.create',
            'programs.update',
            'programs.delete',
            'courses.view',
            'courses.create',
            'courses.update',
            'courses.delete',
            'curriculums.view',
            'curriculums.create',
            'curriculums.update',
            'curriculums.delete',
            'teaching_assignments.view',
            'teaching_assignments.create',
            'teaching_assignments.update',
            'teaching_assignments.delete',
            'timetables.view',
            'timetables.create',
            'timetables.update',
            'timetables.delete',
            'attendance_sessions.view',
            'attendance_sessions.create',
            'attendance_sessions.update',
            'attendance_sessions.delete',
            'attendance_records.view',
            'attendance_records.create',
            'attendance_records.update',
            'attendance_records.delete',
            'exam_terms.view',
            'exam_terms.create',
            'exam_terms.update',
            'exam_terms.delete',
            'exam_schedules.view',
            'exam_schedules.create',
            'exam_schedules.update',
            'exam_schedules.delete',
            'assessment_components.view',
            'assessment_components.create',
            'assessment_components.update',
            'assessment_components.delete',
            'student_marks.view',
            'student_marks.create',
            'student_marks.update',
            'student_marks.delete',
            'grade_scales.view',
            'grade_scales.create',
            'grade_scales.update',
            'grade_scales.delete',
            'grade_scale_rules.view',
            'grade_scale_rules.create',
            'grade_scale_rules.update',
            'grade_scale_rules.delete',
            'student_course_results.view',
            'student_course_results.create',
            'student_course_results.update',
            'student_course_results.delete',
            'buildings.view',
            'buildings.create',
            'buildings.update',
            'buildings.delete',
            'rooms.view',
            'rooms.create',
            'rooms.update',
            'rooms.delete',
            'majors.view',
            'majors.create',
            'majors.update',
            'majors.delete',
            'class_sections.view',
            'class_sections.create',
            'class_sections.update',
            'class_sections.delete',
            'student_enrollments.view',
            'student_enrollments.create',
            'student_enrollments.update',
            'student_enrollments.delete',
            'student_status_histories.view',
            'student_status_histories.create',
            'student_status_histories.update',
            'student_status_histories.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $roles = [
            'super_admin',
            'registrar',
            'department_admin',
            'teacher',
            'student',
            'librarian',
            'hostel_warden',
            'finance_officer',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        Role::findByName('super_admin', 'web')->syncPermissions($permissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
