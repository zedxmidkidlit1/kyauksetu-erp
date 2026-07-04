<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\AnnouncementAudience;
use App\Models\ClassSection;
use App\Models\Course;
use App\Models\Department;
use App\Models\Major;
use App\Models\Program;
use App\Models\Role;
use App\Models\Semester;
use App\Models\StudentCourseResult;
use App\Models\StudentEnrollment;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MobileApiHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_login_issues_token_with_mobile_ability(): void
    {
        [$user] = $this->createStudent('TOKEN');

        $this
            ->postJson('/api/v1/auth/login', [
                'email' => $user->email,
                'password' => 'password',
                'device_name' => 'Flutter test device',
            ])
            ->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonStructure(['data' => ['token']]);

        $this->assertSame(['mobile'], $user->tokens()->firstOrFail()->abilities);
    }

    public function test_protected_mobile_routes_require_mobile_token_ability(): void
    {
        [$user] = $this->createStudent('ABILITY');

        Sanctum::actingAs($user, ['other']);

        $this
            ->getJson('/api/v1/auth/me')
            ->assertForbidden();
    }

    public function test_teacher_token_cannot_access_student_data_routes(): void
    {
        $user = $this->createTeacher('TEACHER');

        Sanctum::actingAs($user, ['mobile']);

        $this
            ->getJson('/api/v1/my-profile')
            ->assertForbidden();
    }

    public function test_student_list_endpoints_support_pagination_and_date_filters(): void
    {
        [$user, , $enrollment, $academicYear, $semester, $course] = $this->createStudent('FILTER');

        $lateCourse = Course::create([
            'code' => 'COURSELATEFILTER',
            'name' => 'Late Course FILTER',
            'credit_hours' => 3,
            'status' => 'active',
        ]);

        StudentCourseResult::create([
            'student_enrollment_id' => $enrollment->id,
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id,
            'course_id' => $course->id,
            'total_marks' => 70,
            'percentage' => 70,
            'grade' => 'EARLY',
            'grade_point' => 3,
            'result_status' => 'published',
            'approved_at' => '2026-06-01 09:00:00',
        ]);

        StudentCourseResult::create([
            'student_enrollment_id' => $enrollment->id,
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id,
            'course_id' => $lateCourse->id,
            'total_marks' => 90,
            'percentage' => 90,
            'grade' => 'LATE',
            'grade_point' => 4,
            'result_status' => 'published',
            'approved_at' => '2026-07-01 09:00:00',
        ]);

        Sanctum::actingAs($user, ['mobile']);

        $response = $this
            ->getJson('/api/v1/my-results?from=2026-07-01&to=2026-07-31&per_page=1')
            ->assertOk()
            ->assertJsonPath('data.0.grade', 'LATE')
            ->assertJsonPath('meta.per_page', 1);

        $this->assertStringNotContainsString('EARLY', $response->getContent());
    }

    public function test_notifications_endpoint_returns_visible_announcements(): void
    {
        [$user] = $this->createStudent('NOTICE');

        $visibleAnnouncement = Announcement::create([
            'title' => 'Visible mobile notice',
            'body' => 'This announcement should be exposed as a notification.',
            'announcement_type' => 'general',
            'priority' => 'high',
            'status' => 'published',
            'publish_at' => '2026-07-01 08:00:00',
        ]);

        AnnouncementAudience::create([
            'announcement_id' => $visibleAnnouncement->id,
            'audience_type' => 'role',
            'role_name' => 'student',
        ]);

        $hiddenAnnouncement = Announcement::create([
            'title' => 'Teacher only notice',
            'body' => 'This should not be visible to the student.',
            'announcement_type' => 'general',
            'priority' => 'normal',
            'status' => 'published',
            'publish_at' => '2026-07-01 08:00:00',
        ]);

        AnnouncementAudience::create([
            'announcement_id' => $hiddenAnnouncement->id,
            'audience_type' => 'role',
            'role_name' => 'teacher',
        ]);

        Sanctum::actingAs($user, ['mobile']);

        $response = $this
            ->getJson('/api/v1/notifications?per_page=1')
            ->assertOk()
            ->assertJsonPath('data.0.id', "announcement:{$visibleAnnouncement->id}")
            ->assertJsonPath('data.0.type', 'announcement')
            ->assertJsonPath('data.0.priority', 'high')
            ->assertJsonPath('meta.per_page', 1);

        $this->assertStringNotContainsString('Teacher only notice', $response->getContent());
    }

    /**
     * @return array{0: User, 1: StudentProfile, 2: StudentEnrollment, 3: AcademicYear, 4: Semester, 5: Course, 6: ClassSection}
     */
    private function createStudent(string $suffix): array
    {
        Role::firstOrCreate([
            'name' => 'student',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create([
            'name' => "Student {$suffix}",
            'email' => "student-{$suffix}@example.test",
        ]);
        $user->assignRole('student');

        [$department, $program, $major, $academicYear, $semester, $classSection, $course] = $this->academicSet($suffix);

        $profile = StudentProfile::create([
            'user_id' => $user->id,
            'student_no' => "STU-{$suffix}",
            'roll_no' => "STU-{$suffix}",
            'institutional_email' => "student-{$suffix}@school.test",
            'first_name' => 'Student',
            'last_name' => $suffix,
            'department_id' => $department->id,
            'program_id' => $program->id,
            'major_id' => $major->id,
            'academic_year_id' => $academicYear->id,
            'class_section_id' => $classSection->id,
            'admission_year' => 2026,
            'status' => 'active',
            'enrolled_at' => '2026-06-01',
        ]);

        $enrollment = StudentEnrollment::create([
            'student_profile_id' => $profile->id,
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

        return [$user, $profile, $enrollment, $academicYear, $semester, $course, $classSection];
    }

    private function createTeacher(string $suffix): User
    {
        Role::firstOrCreate([
            'name' => 'teacher',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create([
            'name' => "Teacher {$suffix}",
            'email' => "teacher-{$suffix}@example.test",
        ]);
        $user->assignRole('teacher');

        [$department] = $this->academicSet($suffix);

        TeacherProfile::create([
            'user_id' => $user->id,
            'staff_no' => "TCH-{$suffix}",
            'institutional_email' => "teacher-{$suffix}@school.test",
            'department_id' => $department->id,
            'position' => 'Lecturer',
            'rank' => 'Faculty',
            'status' => 'active',
        ]);

        return $user;
    }

    /**
     * @return array{0: Department, 1: Program, 2: Major, 3: AcademicYear, 4: Semester, 5: ClassSection, 6: Course}
     */
    private function academicSet(string $suffix): array
    {
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
            'code' => "COURSE{$suffix}",
            'name' => "Course {$suffix}",
            'credit_hours' => 3,
            'status' => 'active',
        ]);

        return [$department, $program, $major, $academicYear, $semester, $classSection, $course];
    }
}
