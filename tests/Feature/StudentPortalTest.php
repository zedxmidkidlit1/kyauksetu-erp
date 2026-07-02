<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\Course;
use App\Models\Department;
use App\Models\FeeType;
use App\Models\Major;
use App\Models\Program;
use App\Models\Role;
use App\Models\Semester;
use App\Models\StudentCourseResult;
use App\Models\StudentEnrollment;
use App\Models\StudentFee;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_student_portal_redirects_to_login(): void
    {
        $this
            ->get(route('student.dashboard'))
            ->assertRedirect(route('student.login'));
    }

    public function test_non_student_user_is_blocked(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->get(route('student.dashboard'))
            ->assertForbidden();
    }

    public function test_student_can_access_own_portal(): void
    {
        [$user, $profile] = $this->createStudent('student-one@example.test', 'STU-001', 'Own Tuition', 'A');

        $this
            ->actingAs($user)
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee($profile->student_no)
            ->assertSee('Current enrollment');
    }

    public function test_student_pages_do_not_show_another_students_data(): void
    {
        [$user] = $this->createStudent('student-one@example.test', 'STU-001', 'Own Tuition', 'A');
        $this->createStudent('student-two@example.test', 'STU-002', 'Other Secret Fee', 'X-OTHER');

        $this
            ->actingAs($user)
            ->get(route('student.fees'))
            ->assertOk()
            ->assertSee('Own Tuition')
            ->assertDontSee('Other Secret Fee');

        $this
            ->actingAs($user)
            ->get(route('student.results'))
            ->assertOk()
            ->assertSee('A')
            ->assertDontSee('X-OTHER');
    }

    /**
     * @return array{0: User, 1: StudentProfile}
     */
    private function createStudent(string $email, string $studentNo, string $feeName, string $grade): array
    {
        Role::firstOrCreate([
            'name' => 'student',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create([
            'email' => $email,
        ]);
        $user->assignRole('student');

        $department = Department::firstOrCreate(
            ['code' => 'IT'],
            ['name' => 'Information Technology', 'is_active' => true],
        );
        $program = Program::firstOrCreate(
            ['code' => 'BCS'],
            ['name' => 'Bachelor of Computer Science', 'duration_years' => 4, 'status' => 'active'],
        );
        $major = Major::firstOrCreate(
            ['department_id' => $department->id, 'program_id' => $program->id, 'code' => 'SE'],
            ['name' => 'Software Engineering', 'status' => 'active'],
        );
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2026-2027'],
            ['start_date' => '2026-06-01', 'end_date' => '2027-03-31', 'status' => 'active'],
        );
        $semester = Semester::firstOrCreate(
            ['academic_year_id' => $academicYear->id, 'name' => 'Semester 1'],
            ['start_date' => '2026-06-01', 'end_date' => '2026-10-31', 'status' => 'active'],
        );
        $classSection = ClassSection::firstOrCreate(
            ['academic_year_id' => $academicYear->id, 'major_id' => $major->id, 'year_level' => 1, 'section' => 'A'],
            ['name' => 'Year 1 A', 'status' => 'active'],
        );
        $course = Course::firstOrCreate(
            ['code' => 'CS101'],
            ['name' => 'Computer Science 101', 'credit_hours' => 3, 'status' => 'active'],
        );
        $feeType = FeeType::create([
            'name' => $feeName,
            'code' => str($studentNo)->replace('-', '_')->append('_FEE')->toString(),
            'amount' => 100000,
            'fee_category' => 'tuition',
            'status' => 'active',
        ]);

        $profile = StudentProfile::create([
            'user_id' => $user->id,
            'student_no' => $studentNo,
            'first_name' => 'Student',
            'last_name' => $studentNo,
            'institutional_email' => $email,
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
            'roll_no' => $studentNo,
            'status' => 'active',
            'enrolled_at' => '2026-06-01',
        ]);

        StudentFee::create([
            'student_profile_id' => $profile->id,
            'student_enrollment_id' => $enrollment->id,
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id,
            'fee_type_id' => $feeType->id,
            'amount' => 100000,
            'discount_amount' => 0,
            'payable_amount' => 100000,
            'due_at' => '2026-07-15',
            'fee_status' => 'pending',
        ]);

        StudentCourseResult::create([
            'student_enrollment_id' => $enrollment->id,
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id,
            'course_id' => $course->id,
            'total_marks' => 90,
            'percentage' => 90,
            'grade' => $grade,
            'grade_point' => 4,
            'result_status' => 'published',
        ]);

        return [$user, $profile];
    }
}
