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

class StudentApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array<int, string>
     */
    private const ENDPOINTS = [
        '/api/v1/me',
        '/api/v1/my-profile',
        '/api/v1/my-enrollment',
        '/api/v1/my-timetable',
        '/api/v1/my-attendance',
        '/api/v1/my-results',
        '/api/v1/my-fees',
        '/api/v1/my-library',
        '/api/v1/my-hostel',
        '/api/v1/announcements',
    ];

    public function test_unauthenticated_student_api_requests_are_blocked(): void
    {
        foreach (self::ENDPOINTS as $endpoint) {
            $this
                ->getJson($endpoint)
                ->assertUnauthorized();
        }
    }

    public function test_authenticated_student_can_read_api_foundation_endpoints(): void
    {
        [$user] = $this->createStudentDataset('OWN');

        Sanctum::actingAs($user, ['mobile']);

        foreach (self::ENDPOINTS as $endpoint) {
            $this
                ->getJson($endpoint)
                ->assertOk()
                ->assertJsonStructure(['data']);
        }
    }

    public function test_authenticated_user_without_student_profile_is_forbidden(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['mobile']);

        $this
            ->getJson('/api/v1/my-profile')
            ->assertForbidden();
    }

    public function test_student_api_responses_are_scoped_to_the_authenticated_student(): void
    {
        [$user] = $this->createStudentDataset('OWN');
        $this->createStudentDataset('OTHER');

        Sanctum::actingAs($user, ['mobile']);

        $this
            ->getJson('/api/v1/my-profile')
            ->assertOk()
            ->assertJsonPath('data.student_no', 'STU-OWN');

        $this->assertEndpointContainsOnlyOwnLabel('/api/v1/my-enrollment', 'STU-OWN', 'STU-OTHER');
        $this->assertEndpointContainsOnlyOwnLabel('/api/v1/my-timetable', 'Timetable OWN', 'Timetable OTHER');
        $this->assertEndpointContainsOnlyOwnLabel('/api/v1/my-attendance', 'Attendance OWN', 'Attendance OTHER');
        $this->assertEndpointContainsOnlyOwnLabel('/api/v1/my-results', 'GRADE-OWN', 'GRADE-OTHER');
        $this->assertEndpointContainsOnlyOwnLabel('/api/v1/my-fees', 'Fee OWN', 'Fee OTHER');
        $this->assertEndpointContainsOnlyOwnLabel('/api/v1/my-library', 'Book OWN', 'Book OTHER');
        $this->assertEndpointContainsOnlyOwnLabel('/api/v1/my-hostel', 'Hostel OWN', 'Hostel OTHER');
        $this->assertEndpointContainsOnlyOwnLabel('/api/v1/announcements', 'Announcement OWN', 'Announcement OTHER');
    }

    private function assertEndpointContainsOnlyOwnLabel(string $endpoint, string $ownLabel, string $otherLabel): void
    {
        $response = $this
            ->getJson($endpoint)
            ->assertOk();

        $content = $response->getContent();

        $this->assertStringContainsString($ownLabel, $content);
        $this->assertStringNotContainsString($otherLabel, $content);
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
        $this->createAttendanceData($suffix, $enrollment, $academicYear, $semester, $classSection, $course);
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
            'day_of_week' => 'monday',
            'starts_at' => '09:00',
            'ends_at' => '10:00',
            'slot_type' => 'lecture',
            'status' => 'scheduled',
        ]);
    }

    private function createAttendanceData(string $suffix, StudentEnrollment $enrollment, AcademicYear $academicYear, Semester $semester, ClassSection $classSection, Course $course): void
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
            'remarks' => "Attendance {$suffix}",
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
