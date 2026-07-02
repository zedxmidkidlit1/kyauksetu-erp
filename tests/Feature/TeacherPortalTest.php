<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\ClassSection;
use App\Models\Course;
use App\Models\Department;
use App\Models\Major;
use App\Models\Program;
use App\Models\Role;
use App\Models\Semester;
use App\Models\StudentEnrollment;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\TeachingAssignment;
use App\Models\Timetable;
use App\Models\TimetableSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_teacher_portal_redirects_to_login(): void
    {
        $this
            ->get(route('teacher.dashboard'))
            ->assertRedirect(route('teacher.login'));
    }

    public function test_non_teacher_user_is_blocked(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->get(route('teacher.dashboard'))
            ->assertForbidden();
    }

    public function test_teacher_can_access_own_portal(): void
    {
        [$user, $profile] = $this->createTeacherDataset('OWN');

        $this
            ->actingAs($user)
            ->get(route('teacher.dashboard'))
            ->assertOk()
            ->assertSee($profile->staff_no)
            ->assertSee('Assignments');
    }

    public function test_teacher_pages_show_only_own_assignments_classes_and_timetable(): void
    {
        [$user] = $this->createTeacherDataset('OWN');
        $this->createTeacherDataset('OTHER');

        $this
            ->actingAs($user)
            ->get(route('teacher.assignments'))
            ->assertOk()
            ->assertSee('Course OWN')
            ->assertDontSee('Course OTHER');

        $this
            ->actingAs($user)
            ->get(route('teacher.classes'))
            ->assertOk()
            ->assertSee('STU-OWN')
            ->assertDontSee('STU-OTHER');

        $this
            ->actingAs($user)
            ->get(route('teacher.timetable'))
            ->assertOk()
            ->assertSee('Timetable OWN')
            ->assertDontSee('Timetable OTHER');
    }

    public function test_teacher_announcements_are_scoped_to_visible_audiences(): void
    {
        [$user] = $this->createTeacherDataset('OWN');
        $this->createTeacherDataset('OTHER');

        Announcement::create([
            'title' => 'Teacher Notice',
            'body' => 'Visible to teachers',
            'status' => 'published',
            'publish_at' => now()->subDay(),
            'created_by' => $user->id,
        ])->audiences()->create([
            'audience_type' => 'role',
            'role_name' => 'teacher',
        ]);

        Announcement::create([
            'title' => 'Other Student Notice',
            'body' => 'Hidden from teachers',
            'status' => 'published',
            'publish_at' => now()->subDay(),
            'created_by' => $user->id,
        ])->audiences()->create([
            'audience_type' => 'role',
            'role_name' => 'student',
        ]);

        $this
            ->actingAs($user)
            ->get(route('teacher.announcements'))
            ->assertOk()
            ->assertSee('Teacher Notice')
            ->assertDontSee('Other Student Notice');
    }

    /**
     * @return array{0: User, 1: TeacherProfile}
     */
    private function createTeacherDataset(string $suffix): array
    {
        Role::firstOrCreate([
            'name' => 'teacher',
            'guard_name' => 'web',
        ]);

        $teacherUser = User::factory()->create([
            'name' => "Teacher {$suffix}",
            'email' => "teacher-{$suffix}@example.test",
        ]);
        $teacherUser->assignRole('teacher');

        $department = Department::create([
            'name' => "Department {$suffix}",
            'code' => "D{$suffix}",
            'is_active' => true,
        ]);
        $program = Program::create([
            'name' => "Program {$suffix}",
            'code' => "P{$suffix}",
            'duration_years' => 4,
            'status' => 'active',
        ]);
        $major = Major::create([
            'department_id' => $department->id,
            'program_id' => $program->id,
            'name' => "Major {$suffix}",
            'code' => "M{$suffix}",
            'status' => 'active',
        ]);
        $academicYear = AcademicYear::create([
            'name' => "2026-2027 {$suffix}",
            'start_date' => '2026-06-01',
            'end_date' => '2027-03-31',
            'status' => 'active',
        ]);
        $semester = Semester::create([
            'academic_year_id' => $academicYear->id,
            'name' => "Semester {$suffix}",
            'start_date' => '2026-06-01',
            'end_date' => '2026-10-31',
            'status' => 'active',
        ]);
        $classSection = ClassSection::create([
            'academic_year_id' => $academicYear->id,
            'major_id' => $major->id,
            'name' => "Class {$suffix}",
            'year_level' => 1,
            'section' => $suffix,
            'status' => 'active',
        ]);
        $course = Course::create([
            'name' => "Course {$suffix}",
            'code' => "C{$suffix}",
            'credit_hours' => 3,
            'status' => 'active',
        ]);
        $teacherProfile = TeacherProfile::create([
            'user_id' => $teacherUser->id,
            'staff_no' => "TCH-{$suffix}",
            'institutional_email' => $teacherUser->email,
            'department_id' => $department->id,
            'position' => 'Lecturer',
            'rank' => 'Faculty',
            'status' => 'active',
        ]);
        $assignment = TeachingAssignment::create([
            'teacher_profile_id' => $teacherProfile->id,
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id,
            'program_id' => $program->id,
            'major_id' => $major->id,
            'class_section_id' => $classSection->id,
            'course_id' => $course->id,
            'status' => 'active',
            'starts_at' => '2026-06-01',
        ]);

        $studentUser = User::factory()->create([
            'name' => "Student {$suffix}",
            'email' => "student-{$suffix}@example.test",
        ]);
        $studentProfile = StudentProfile::create([
            'user_id' => $studentUser->id,
            'student_no' => "STU-{$suffix}",
            'first_name' => 'Student',
            'last_name' => $suffix,
            'institutional_email' => $studentUser->email,
            'department_id' => $department->id,
            'program_id' => $program->id,
            'major_id' => $major->id,
            'academic_year_id' => $academicYear->id,
            'class_section_id' => $classSection->id,
            'admission_year' => 2026,
            'status' => 'active',
            'enrolled_at' => '2026-06-01',
        ]);
        StudentEnrollment::create([
            'student_profile_id' => $studentProfile->id,
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id,
            'program_id' => $program->id,
            'major_id' => $major->id,
            'class_section_id' => $classSection->id,
            'year_level' => 1,
            'roll_no' => "STU-{$suffix}",
            'status' => 'active',
            'enrolled_at' => '2026-06-01',
        ]);

        $timetable = Timetable::create([
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id,
            'program_id' => $program->id,
            'major_id' => $major->id,
            'class_section_id' => $classSection->id,
            'name' => "Timetable {$suffix}",
            'effective_from' => '2026-06-01',
            'status' => 'active',
        ]);
        TimetableSlot::create([
            'timetable_id' => $timetable->id,
            'teaching_assignment_id' => $assignment->id,
            'course_id' => $course->id,
            'teacher_profile_id' => $teacherProfile->id,
            'day_of_week' => 'monday',
            'starts_at' => '09:00:00',
            'ends_at' => '10:00:00',
            'slot_type' => 'lecture',
            'status' => 'scheduled',
        ]);

        Announcement::create([
            'title' => "Class Notice {$suffix}",
            'body' => "Visible to class {$suffix}",
            'status' => 'published',
            'publish_at' => now()->subDay(),
            'created_by' => $teacherUser->id,
        ])->audiences()->create([
            'audience_type' => 'class_section',
            'class_section_id' => $classSection->id,
        ]);

        return [$teacherUser, $teacherProfile];
    }
}
