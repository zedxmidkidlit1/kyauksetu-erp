<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
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
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherAttendanceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_teacher_attendance_redirects_to_login(): void
    {
        $this
            ->get(route('teacher.attendance.index'))
            ->assertRedirect(route('teacher.login'));
    }

    public function test_non_teacher_user_is_blocked_from_attendance(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->get(route('teacher.attendance.index'))
            ->assertForbidden();
    }

    public function test_teacher_can_create_session_for_own_assignment(): void
    {
        [$user, , $assignment] = $this->createTeacherDataset('OWN');

        $response = $this
            ->actingAs($user)
            ->post(route('teacher.attendance.sessions.store'), [
                'teaching_assignment_id' => $assignment->id,
                'session_date' => '2026-07-02',
                'starts_at' => '09:00',
                'ends_at' => '10:00',
            ]);

        $session = AttendanceSession::query()->first();

        $response->assertRedirect(route('teacher.attendance.sessions.show', $session));
        $this->assertSame($assignment->id, $session->teaching_assignment_id);
        $this->assertSame(2, $session->records()->count());
    }

    public function test_teacher_cannot_create_view_or_update_another_teachers_attendance(): void
    {
        [$user] = $this->createTeacherDataset('OWN');
        [, $otherProfile, $otherAssignment] = $this->createTeacherDataset('OTHER');
        $otherSession = $this->createSessionFor($otherProfile, $otherAssignment);
        $otherRecord = $otherSession->records()->firstOrFail();

        $this
            ->actingAs($user)
            ->post(route('teacher.attendance.sessions.store'), [
                'teaching_assignment_id' => $otherAssignment->id,
                'session_date' => '2026-07-02',
            ])
            ->assertNotFound();

        $this
            ->actingAs($user)
            ->get(route('teacher.attendance.sessions.show', $otherSession))
            ->assertForbidden();

        $this
            ->actingAs($user)
            ->post(route('teacher.attendance.sessions.records.update', $otherSession), [
                'records' => [
                    $otherRecord->id => ['status' => 'absent'],
                ],
            ])
            ->assertForbidden();
    }

    public function test_attendance_records_are_created_and_updated_only_for_assigned_class_students(): void
    {
        [$user, , $assignment, $assignedEnrollments] = $this->createTeacherDataset('OWN');
        $this->createUnassignedStudent('OUTSIDE');

        $this
            ->actingAs($user)
            ->post(route('teacher.attendance.sessions.store'), [
                'teaching_assignment_id' => $assignment->id,
                'session_date' => '2026-07-02',
            ]);

        $session = AttendanceSession::query()->firstOrFail();

        $this->assertSame(2, $session->records()->count());
        $this->assertEqualsCanonicalizing(
            $assignedEnrollments->pluck('id')->all(),
            $session->records()->pluck('student_enrollment_id')->all(),
        );

        $records = $session->records()->get();

        $this
            ->actingAs($user)
            ->post(route('teacher.attendance.sessions.records.update', $session), [
                'records' => [
                    $records[0]->id => ['status' => 'late', 'remarks' => 'Arrived after roll call'],
                    $records[1]->id => ['status' => 'excused'],
                ],
            ])
            ->assertRedirect(route('teacher.attendance.sessions.show', $session));

        $this->assertSame('late', $records[0]->fresh()->status);
        $this->assertSame('excused', $records[1]->fresh()->status);
        $this->assertSame(0, AttendanceRecord::query()
            ->whereHas('studentEnrollment.studentProfile', fn ($query) => $query->where('student_no', 'STU-OUTSIDE'))
            ->count());
    }

    /**
     * @return array{0: User, 1: TeacherProfile, 2: TeachingAssignment, 3: Collection<int, StudentEnrollment>}
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

        [$department, $program, $major, $academicYear, $semester, $classSection, $course] = $this->academicSet($suffix);

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

        $enrollments = collect([
            $this->createStudentEnrollment("{$suffix}-A", $department, $program, $major, $academicYear, $semester, $classSection),
            $this->createStudentEnrollment("{$suffix}-B", $department, $program, $major, $academicYear, $semester, $classSection),
        ]);

        return [$teacherUser, $teacherProfile, $assignment, $enrollments];
    }

    private function createSessionFor(TeacherProfile $profile, TeachingAssignment $assignment): AttendanceSession
    {
        $session = AttendanceSession::create([
            'academic_year_id' => $assignment->academic_year_id,
            'semester_id' => $assignment->semester_id,
            'class_section_id' => $assignment->class_section_id,
            'teaching_assignment_id' => $assignment->id,
            'course_id' => $assignment->course_id,
            'teacher_profile_id' => $profile->id,
            'session_date' => '2026-07-02',
            'status' => 'draft',
        ]);

        StudentEnrollment::query()
            ->where('class_section_id', $assignment->class_section_id)
            ->get()
            ->each(fn (StudentEnrollment $enrollment) => AttendanceRecord::create([
                'attendance_session_id' => $session->id,
                'student_enrollment_id' => $enrollment->id,
                'status' => 'present',
            ]));

        return $session;
    }

    private function createUnassignedStudent(string $suffix): StudentEnrollment
    {
        [$department, $program, $major, $academicYear, $semester, $classSection] = $this->academicSet($suffix);

        return $this->createStudentEnrollment($suffix, $department, $program, $major, $academicYear, $semester, $classSection);
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
            'name' => "Course {$suffix}",
            'code' => "C{$suffix}",
            'credit_hours' => 3,
            'status' => 'active',
        ]);

        return [$department, $program, $major, $academicYear, $semester, $classSection, $course];
    }

    private function createStudentEnrollment(
        string $suffix,
        Department $department,
        Program $program,
        Major $major,
        AcademicYear $academicYear,
        Semester $semester,
        ClassSection $classSection
    ): StudentEnrollment {
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

        return StudentEnrollment::create([
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
    }
}
