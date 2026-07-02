<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\AssessmentComponent;
use App\Models\AttendanceSession;
use App\Models\Building;
use App\Models\ClassSection;
use App\Models\Course;
use App\Models\Department;
use App\Models\Major;
use App\Models\Program;
use App\Models\Role;
use App\Models\Room;
use App\Models\Semester;
use App\Models\StudentEnrollment;
use App\Models\StudentMark;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\TeachingAssignment;
use App\Models\Timetable;
use App\Models\TimetableSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class KaiTeacherContextTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_kai_teacher_context_and_chat_requests_are_blocked(): void
    {
        $this->getJson('/api/v1/kai/context')->assertUnauthorized();
        $this->postJson('/api/v1/kai/chat', ['message' => 'Show my schedule'])->assertUnauthorized();
    }

    public function test_existing_student_kai_context_and_chat_still_work(): void
    {
        $user = $this->createStudentUser('OWN');

        Sanctum::actingAs($user);

        $this
            ->getJson('/api/v1/kai/context')
            ->assertOk()
            ->assertJsonPath('data.student_profile.student_no', 'STU-OWN');

        $this
            ->postJson('/api/v1/kai/chat', ['message' => 'Show my fees'])
            ->assertOk()
            ->assertJsonPath('data.reply', 'You have 0 unpaid or due fee item(s) totaling 0.00.')
            ->assertJsonPath('data.context_used.keys.1', 'student_profile');
    }

    public function test_teacher_receives_own_kai_context(): void
    {
        $this->travelTo('2026-07-02 08:00:00');

        [$user] = $this->createTeacherDataset('OWN');

        Sanctum::actingAs($user);

        $this
            ->getJson('/api/v1/kai/context')
            ->assertOk()
            ->assertJsonPath('data.teacher_profile.staff_no', 'TCH-OWN')
            ->assertJsonPath('data.teaching_assignments.count', 1)
            ->assertJsonPath('data.today_upcoming_timetable.today', 'Thursday')
            ->assertJsonPath('data.assigned_classes.student_count', 2)
            ->assertJsonPath('data.recent_attendance_sessions.count', 1)
            ->assertJsonPath('data.assessment_components_pending_marks.pending_count', 1)
            ->assertJsonPath('data.visible_announcements.count', 1);
    }

    public function test_teacher_kai_context_does_not_leak_another_teachers_data(): void
    {
        $this->travelTo('2026-07-02 08:00:00');

        [$user] = $this->createTeacherDataset('OWN');
        $this->createTeacherDataset('OTHER');

        Sanctum::actingAs($user);

        $content = $this
            ->getJson('/api/v1/kai/context')
            ->assertOk()
            ->getContent();

        foreach (['TCH-OWN', 'Course OWN', 'Class OWN', 'Component OWN', 'Announcement OWN'] as $label) {
            $this->assertStringContainsString($label, $content);
        }

        foreach (['TCH-OTHER', 'Course OTHER', 'Class OTHER', 'Component OTHER', 'Announcement OTHER', 'STU-OTHER'] as $label) {
            $this->assertStringNotContainsString($label, $content);
        }
    }

    public function test_teacher_kai_chat_returns_safe_compact_json(): void
    {
        $this->travelTo('2026-07-02 08:00:00');

        [$user] = $this->createTeacherDataset('OWN');

        Sanctum::actingAs($user);

        $this
            ->postJson('/api/v1/kai/chat', ['message' => 'Show my teaching schedule'])
            ->assertOk()
            ->assertJsonPath('data.reply', 'I found 1 upcoming timetable item(s) in your teacher context.')
            ->assertJsonPath('data.context_used.keys.1', 'teacher_profile')
            ->assertJsonMissingPath('data.context_used.teacher_profile')
            ->assertJsonMissingPath('data.context_used.user.email');
    }

    public function test_unsupported_role_is_blocked_from_kai_context_and_chat(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/v1/kai/context')->assertForbidden();
        $this->postJson('/api/v1/kai/chat', ['message' => 'Hello KAI'])->assertForbidden();
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

        [$department, $program, $major, $academicYear, , $classSection] = $this->academicSet("STU{$suffix}");

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

    /**
     * @return array{0: User, 1: TeacherProfile, 2: TeachingAssignment}
     */
    private function createTeacherDataset(string $suffix): array
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

        [$department, $program, $major, $academicYear, $semester, $classSection, $course] = $this->academicSet($suffix);
        $profile = TeacherProfile::create([
            'user_id' => $user->id,
            'staff_no' => "TCH-{$suffix}",
            'institutional_email' => "teacher-{$suffix}@school.test",
            'department_id' => $department->id,
            'position' => 'Lecturer',
            'rank' => 'Faculty',
            'status' => 'active',
        ]);
        $assignment = TeachingAssignment::create([
            'teacher_profile_id' => $profile->id,
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

        $this->createTimetable($suffix, $profile, $assignment, $academicYear, $semester, $program, $major, $classSection, $course);
        $this->createAttendance($profile, $assignment, $academicYear, $semester, $classSection, $course);
        $this->createAssessment($suffix, $enrollments[0], $academicYear, $semester, $classSection, $course);
        $this->createAnnouncement($suffix, $classSection, $user);

        return [$user, $profile, $assignment];
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
            'code' => "COURSE{$suffix}",
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

    private function createTimetable(
        string $suffix,
        TeacherProfile $profile,
        TeachingAssignment $assignment,
        AcademicYear $academicYear,
        Semester $semester,
        Program $program,
        Major $major,
        ClassSection $classSection,
        Course $course
    ): void {
        $building = Building::create([
            'name' => "Building {$suffix}",
            'code' => "BLD{$suffix}",
            'status' => 'active',
        ]);
        $room = Room::create([
            'building_id' => $building->id,
            'name' => "Room {$suffix}",
            'code' => "ROOM{$suffix}",
            'room_type' => 'classroom',
            'capacity' => 30,
            'status' => 'active',
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
            'teacher_profile_id' => $profile->id,
            'room_id' => $room->id,
            'day_of_week' => 'thursday',
            'starts_at' => '09:00',
            'ends_at' => '10:00',
            'slot_type' => 'lecture',
            'status' => 'scheduled',
        ]);
    }

    private function createAttendance(TeacherProfile $profile, TeachingAssignment $assignment, AcademicYear $academicYear, Semester $semester, ClassSection $classSection, Course $course): void
    {
        AttendanceSession::create([
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id,
            'class_section_id' => $classSection->id,
            'teaching_assignment_id' => $assignment->id,
            'course_id' => $course->id,
            'teacher_profile_id' => $profile->id,
            'session_date' => '2026-07-01',
            'status' => 'completed',
        ]);
    }

    private function createAssessment(string $suffix, StudentEnrollment $markedEnrollment, AcademicYear $academicYear, Semester $semester, ClassSection $classSection, Course $course): void
    {
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

        StudentMark::create([
            'assessment_component_id' => $component->id,
            'student_enrollment_id' => $markedEnrollment->id,
            'marks_obtained' => 40,
            'status' => 'draft',
        ]);
    }

    private function createAnnouncement(string $suffix, ClassSection $classSection, User $createdBy): void
    {
        Announcement::create([
            'title' => "Announcement {$suffix}",
            'body' => "Visible to class {$suffix}",
            'status' => 'published',
            'publish_at' => now()->subDay(),
            'created_by' => $createdBy->id,
        ])->audiences()->create([
            'audience_type' => 'class_section',
            'class_section_id' => $classSection->id,
        ]);
    }
}
