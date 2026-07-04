<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\AnnouncementAudience;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookCopy;
use App\Models\Building;
use App\Models\ClassSection;
use App\Models\Course;
use App\Models\Department;
use App\Models\FeeType;
use App\Models\Hostel;
use App\Models\HostelAllocation;
use App\Models\HostelBed;
use App\Models\HostelRoom;
use App\Models\LibraryLoan;
use App\Models\Major;
use App\Models\Program;
use App\Models\Role;
use App\Models\Room;
use App\Models\Semester;
use App\Models\StudentCourseResult;
use App\Models\StudentEnrollment;
use App\Models\StudentFee;
use App\Models\StudentProfile;
use App\Models\Timetable;
use App\Models\TimetableSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class KaiContextTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_kai_context_request_is_blocked(): void
    {
        $this
            ->getJson('/api/v1/kai/context')
            ->assertUnauthorized();
    }

    public function test_non_student_user_is_blocked(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['mobile']);

        $this
            ->getJson('/api/v1/kai/context')
            ->assertForbidden();
    }

    public function test_student_receives_own_kai_context(): void
    {
        $this->travelTo('2026-07-02 08:00:00');

        [$user] = $this->createStudentDataset('OWN');

        Sanctum::actingAs($user, ['mobile']);

        $this
            ->getJson('/api/v1/kai/context')
            ->assertOk()
            ->assertJsonPath('data.user.email', 'student-OWN@example.test')
            ->assertJsonPath('data.student_profile.student_no', 'STU-OWN')
            ->assertJsonPath('data.current_enrollment.roll_no', 'STU-OWN')
            ->assertJsonPath('data.today_upcoming_timetable.today', 'Thursday')
            ->assertJsonPath('data.visible_announcements.count', 1)
            ->assertJsonPath('data.attendance.present_count', 1)
            ->assertJsonPath('data.latest_results.count', 1)
            ->assertJsonPath('data.unpaid_due_fees.count', 1)
            ->assertJsonPath('data.active_library_loans.count', 1)
            ->assertJsonPath('data.active_hostel_allocation.hostel.name', 'Hostel OWN');
    }

    public function test_kai_context_does_not_leak_another_students_data(): void
    {
        $this->travelTo('2026-07-02 08:00:00');

        [$user] = $this->createStudentDataset('OWN');
        $this->createStudentDataset('OTHER');

        Sanctum::actingAs($user, ['mobile']);

        $response = $this
            ->getJson('/api/v1/kai/context')
            ->assertOk();

        $content = $response->getContent();

        foreach (['STU-OWN', 'Timetable OWN', 'Course OWN', 'GRADE-OWN', 'Fee OWN', 'Book OWN', 'Hostel OWN', 'Announcement OWN'] as $label) {
            $this->assertStringContainsString($label, $content);
        }

        foreach (['STU-OTHER', 'Timetable OTHER', 'Course OTHER', 'GRADE-OTHER', 'Fee OTHER', 'Book OTHER', 'Hostel OTHER', 'Announcement OTHER'] as $label) {
            $this->assertStringNotContainsString($label, $content);
        }
    }

    /**
     * @return array{0: User, 1: StudentProfile, 2: StudentEnrollment}
     */
    private function createStudentDataset(string $suffix): array
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

        $department = Department::create([
            'code' => "D{$suffix}",
            'name' => "Department {$suffix}",
            'is_active' => true,
        ]);
        $program = Program::create([
            'code' => "P{$suffix}",
            'name' => "Program {$suffix}",
            'duration_years' => 4,
            'status' => 'active',
        ]);
        $major = Major::create([
            'department_id' => $department->id,
            'program_id' => $program->id,
            'code' => "M{$suffix}",
            'name' => "Major {$suffix}",
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

        $this->createTimetableData($suffix, $academicYear, $semester, $program, $major, $classSection, $course);
        $this->createAttendanceData($enrollment, $academicYear, $semester, $classSection, $course);
        $this->createResultData($suffix, $enrollment, $academicYear, $semester, $course);
        $this->createFeeData($suffix, $profile, $enrollment, $academicYear, $semester);
        $this->createLibraryData($suffix, $profile);
        $this->createHostelData($suffix, $profile);
        $this->createAnnouncementData($suffix, $classSection);

        return [$user, $profile, $enrollment];
    }

    private function createTimetableData(string $suffix, AcademicYear $academicYear, Semester $semester, Program $program, Major $major, ClassSection $classSection, Course $course): void
    {
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
            'course_id' => $course->id,
            'room_id' => $room->id,
            'day_of_week' => 'thursday',
            'starts_at' => '09:00',
            'ends_at' => '10:00',
            'slot_type' => 'lecture',
            'status' => 'scheduled',
        ]);
    }

    private function createAttendanceData(StudentEnrollment $enrollment, AcademicYear $academicYear, Semester $semester, ClassSection $classSection, Course $course): void
    {
        $session = AttendanceSession::create([
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id,
            'class_section_id' => $classSection->id,
            'course_id' => $course->id,
            'session_date' => '2026-07-01',
            'starts_at' => '09:00',
            'ends_at' => '10:00',
            'status' => 'completed',
        ]);

        AttendanceRecord::create([
            'attendance_session_id' => $session->id,
            'student_enrollment_id' => $enrollment->id,
            'status' => 'present',
            'marked_at' => '2026-07-01 09:15:00',
        ]);
    }

    private function createResultData(string $suffix, StudentEnrollment $enrollment, AcademicYear $academicYear, Semester $semester, Course $course): void
    {
        StudentCourseResult::create([
            'student_enrollment_id' => $enrollment->id,
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id,
            'course_id' => $course->id,
            'total_marks' => 90,
            'percentage' => 90,
            'grade' => "GRADE-{$suffix}",
            'grade_point' => 4,
            'result_status' => 'published',
            'approved_at' => '2026-07-01 10:00:00',
        ]);
    }

    private function createFeeData(string $suffix, StudentProfile $profile, StudentEnrollment $enrollment, AcademicYear $academicYear, Semester $semester): void
    {
        $feeType = FeeType::create([
            'name' => "Fee {$suffix}",
            'code' => "FEE{$suffix}",
            'amount' => 100000,
            'fee_category' => 'tuition',
            'status' => 'active',
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
    }

    private function createLibraryData(string $suffix, StudentProfile $profile): void
    {
        $category = BookCategory::create([
            'name' => "Category {$suffix}",
            'code' => "CAT{$suffix}",
            'status' => 'active',
        ]);
        $book = Book::create([
            'book_category_id' => $category->id,
            'title' => "Book {$suffix}",
            'author' => "Author {$suffix}",
            'status' => 'active',
        ]);
        $copy = BookCopy::create([
            'book_id' => $book->id,
            'accession_no' => "ACC{$suffix}",
            'barcode' => "BAR{$suffix}",
            'copy_status' => 'borrowed',
        ]);

        LibraryLoan::create([
            'book_copy_id' => $copy->id,
            'student_profile_id' => $profile->id,
            'borrowed_at' => '2026-07-01 08:00:00',
            'due_at' => '2026-07-15 08:00:00',
            'loan_status' => 'active',
        ]);
    }

    private function createHostelData(string $suffix, StudentProfile $profile): void
    {
        $hostel = Hostel::create([
            'name' => "Hostel {$suffix}",
            'code' => "H{$suffix}",
            'gender_type' => 'mixed',
            'status' => 'active',
        ]);
        $room = HostelRoom::create([
            'hostel_id' => $hostel->id,
            'name' => "Hostel Room {$suffix}",
            'room_no' => "HR{$suffix}",
            'capacity' => 2,
            'room_type' => 'shared',
            'status' => 'active',
        ]);
        $bed = HostelBed::create([
            'hostel_room_id' => $room->id,
            'bed_no' => "BED{$suffix}",
            'bed_status' => 'occupied',
        ]);

        HostelAllocation::create([
            'student_profile_id' => $profile->id,
            'hostel_id' => $hostel->id,
            'hostel_room_id' => $room->id,
            'hostel_bed_id' => $bed->id,
            'allocated_at' => '2026-07-01 08:00:00',
            'allocation_status' => 'active',
        ]);
    }

    private function createAnnouncementData(string $suffix, ClassSection $classSection): void
    {
        $announcement = Announcement::create([
            'title' => "Announcement {$suffix}",
            'body' => "Announcement body {$suffix}",
            'announcement_type' => 'general',
            'priority' => 'normal',
            'status' => 'published',
            'publish_at' => now()->subDay(),
        ]);

        AnnouncementAudience::create([
            'announcement_id' => $announcement->id,
            'audience_type' => 'class_section',
            'class_section_id' => $classSection->id,
        ]);
    }
}
