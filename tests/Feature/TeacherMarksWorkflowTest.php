<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AssessmentComponent;
use App\Models\ClassSection;
use App\Models\Course;
use App\Models\Department;
use App\Models\Major;
use App\Models\Program;
use App\Models\Role;
use App\Models\Semester;
use App\Models\StudentEnrollment;
use App\Models\StudentMark;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherMarksWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_teacher_marks_redirects_to_login(): void
    {
        $this
            ->get(route('teacher.marks.index'))
            ->assertRedirect(route('teacher.login'));
    }

    public function test_non_teacher_user_is_blocked_from_marks(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->get(route('teacher.marks.index'))
            ->assertForbidden();
    }

    public function test_teacher_can_view_own_mark_components(): void
    {
        [$user] = $this->createTeacherDataset('OWN');
        $this->createTeacherDataset('OTHER');

        $this
            ->actingAs($user)
            ->get(route('teacher.marks.index'))
            ->assertOk()
            ->assertSee('Component OWN')
            ->assertDontSee('Component OTHER');
    }

    public function test_teacher_can_enter_and_update_marks_for_own_assigned_class_students(): void
    {
        [$user, , , $component, $enrollments] = $this->createTeacherDataset('OWN');
        $outsideEnrollment = $this->createUnassignedStudent('OUTSIDE');

        $this
            ->actingAs($user)
            ->post(route('teacher.marks.components.students.update', $component), [
                'records' => [
                    $enrollments[0]->id => ['marks_obtained' => '42.50', 'remarks' => 'Good work'],
                    $enrollments[1]->id => ['marks_obtained' => '36'],
                ],
            ])
            ->assertRedirect(route('teacher.marks.components.show', $component));

        $this->assertSame(2, StudentMark::query()->whereBelongsTo($component)->count());
        $this->assertSame(0, StudentMark::query()->whereBelongsTo($outsideEnrollment)->count());
        $this->assertEquals('42.50', StudentMark::query()
            ->whereBelongsTo($component)
            ->whereBelongsTo($enrollments[0])
            ->firstOrFail()
            ->marks_obtained);

        $this
            ->actingAs($user)
            ->post(route('teacher.marks.components.students.update', $component), [
                'records' => [
                    $enrollments[0]->id => ['marks_obtained' => '44.25', 'remarks' => 'Updated'],
                ],
            ])
            ->assertRedirect(route('teacher.marks.components.show', $component));

        $this->assertSame(2, StudentMark::query()->whereBelongsTo($component)->count());
        $this->assertEquals('44.25', StudentMark::query()
            ->whereBelongsTo($component)
            ->whereBelongsTo($enrollments[0])
            ->firstOrFail()
            ->marks_obtained);
    }

    public function test_teacher_cannot_view_or_update_another_teachers_mark_component_or_students(): void
    {
        [$user, , , $ownComponent] = $this->createTeacherDataset('OWN');
        [, , , $otherComponent, $otherEnrollments] = $this->createTeacherDataset('OTHER');

        $this
            ->actingAs($user)
            ->get(route('teacher.marks.components.show', $otherComponent))
            ->assertForbidden();

        $this
            ->actingAs($user)
            ->post(route('teacher.marks.components.students.update', $otherComponent), [
                'records' => [
                    $otherEnrollments[0]->id => ['marks_obtained' => '20'],
                ],
            ])
            ->assertForbidden();

        $this
            ->actingAs($user)
            ->post(route('teacher.marks.components.students.update', $ownComponent), [
                'records' => [
                    $otherEnrollments[0]->id => ['marks_obtained' => '20'],
                ],
            ])
            ->assertForbidden();

        $this->assertSame(0, StudentMark::query()->whereBelongsTo($otherComponent)->count());
    }

    public function test_marks_cannot_exceed_component_max_marks(): void
    {
        [$user, , , $component, $enrollments] = $this->createTeacherDataset('OWN');

        $this
            ->actingAs($user)
            ->from(route('teacher.marks.components.show', $component))
            ->post(route('teacher.marks.components.students.update', $component), [
                'records' => [
                    $enrollments[0]->id => ['marks_obtained' => '51'],
                ],
            ])
            ->assertRedirect(route('teacher.marks.components.show', $component))
            ->assertSessionHasErrors("records.{$enrollments[0]->id}.marks_obtained");

        $this->assertSame(0, StudentMark::query()->whereBelongsTo($component)->count());
    }

    /**
     * @return array{0: User, 1: TeacherProfile, 2: TeachingAssignment, 3: AssessmentComponent, 4: Collection<int, StudentEnrollment>}
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
        $component = AssessmentComponent::create([
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id,
            'class_section_id' => $classSection->id,
            'course_id' => $course->id,
            'name' => "Component {$suffix}",
            'component_type' => 'assignment',
            'max_marks' => 50,
            'weight' => 10,
            'status' => 'draft',
        ]);
        $enrollments = collect([
            $this->createStudentEnrollment("{$suffix}-A", $department, $program, $major, $academicYear, $semester, $classSection),
            $this->createStudentEnrollment("{$suffix}-B", $department, $program, $major, $academicYear, $semester, $classSection),
        ]);

        return [$teacherUser, $teacherProfile, $assignment, $component, $enrollments];
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
