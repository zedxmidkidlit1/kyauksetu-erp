<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\Department;
use App\Models\Major;
use App\Models\Program;
use App\Models\Role;
use App\Models\Semester;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileAuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_student_login_returns_token(): void
    {
        $user = $this->createStudentUser('OWN');

        $this
            ->postJson('/api/v1/auth/login', [
                'email' => $user->email,
                'password' => 'password',
                'device_name' => 'Flutter test device',
            ])
            ->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.email', $user->email)
            ->assertJsonPath('data.primary_role', 'student')
            ->assertJsonPath('data.profile.type', 'student')
            ->assertJsonPath('data.profile.student_no', 'STU-OWN')
            ->assertJsonStructure(['data' => ['token']]);

        $this->assertSame(1, $user->tokens()->count());
    }

    public function test_valid_teacher_login_returns_token(): void
    {
        $user = $this->createTeacherUser('OWN');

        $this
            ->postJson('/api/v1/auth/login', [
                'email' => $user->email,
                'password' => 'password',
            ])
            ->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.email', $user->email)
            ->assertJsonPath('data.primary_role', 'teacher')
            ->assertJsonPath('data.profile.type', 'teacher')
            ->assertJsonPath('data.profile.staff_no', 'TCH-OWN')
            ->assertJsonStructure(['data' => ['token']]);

        $this->assertSame(1, $user->tokens()->count());
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $user = $this->createStudentUser('BAD');

        $this
            ->postJson('/api/v1/auth/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);

        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_unsupported_role_is_rejected(): void
    {
        $user = User::factory()->create([
            'email' => 'unsupported@example.test',
        ]);

        $this
            ->postJson('/api/v1/auth/login', [
                'email' => $user->email,
                'password' => 'password',
            ])
            ->assertForbidden();

        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_authenticated_auth_me_works(): void
    {
        $user = $this->createTeacherUser('ME');
        $token = $this->loginToken($user);

        $this
            ->withToken($token)
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.user.email', $user->email)
            ->assertJsonPath('data.primary_role', 'teacher')
            ->assertJsonPath('data.profile.staff_no', 'TCH-ME');
    }

    public function test_logout_revokes_current_token(): void
    {
        $user = $this->createStudentUser('LOGOUT');
        $token = $this->loginToken($user);

        $this
            ->withToken($token)
            ->postJson('/api/v1/auth/logout')
            ->assertOk()
            ->assertJsonPath('data.revoked', true);

        $this->assertSame(0, $user->tokens()->count());
        $this->app['auth']->forgetGuards();

        $this
            ->withToken($token)
            ->getJson('/api/v1/auth/me')
            ->assertUnauthorized();
    }

    public function test_token_can_access_kai_context(): void
    {
        $this->travelTo('2026-07-02 08:00:00');

        $user = $this->createTeacherUser('KAI');
        $token = $this->loginToken($user);

        $this
            ->withToken($token)
            ->getJson('/api/v1/kai/context')
            ->assertOk()
            ->assertJsonPath('data.teacher_profile.staff_no', 'TCH-KAI');
    }

    private function loginToken(User $user): string
    {
        return (string) $this
            ->postJson('/api/v1/auth/login', [
                'email' => $user->email,
                'password' => 'password',
                'device_name' => 'Feature test',
            ])
            ->assertOk()
            ->json('data.token');
    }

    private function createStudentUser(string $suffix): User
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

        [$department, $program, $major, $academicYear, $classSection] = $this->academicSet($suffix);

        StudentProfile::create([
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

        return $user;
    }

    private function createTeacherUser(string $suffix): User
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
     * @return array{0: Department, 1: Program, 2: Major, 3: AcademicYear, 4: ClassSection}
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
        Semester::create([
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

        return [$department, $program, $major, $academicYear, $classSection];
    }
}
