<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\AdmissionApplication;
use App\Models\AdmissionBatch;
use App\Models\AdmissionDecision;
use App\Models\Announcement;
use App\Models\AnnouncementAudience;
use App\Models\Applicant;
use App\Models\AssessmentComponent;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookCopy;
use App\Models\Building;
use App\Models\ClassSection;
use App\Models\Course;
use App\Models\Curriculum;
use App\Models\CurriculumCourse;
use App\Models\Department;
use App\Models\FeeType;
use App\Models\GradeScale;
use App\Models\GradeScaleRule;
use App\Models\Hostel;
use App\Models\HostelAllocation;
use App\Models\HostelBed;
use App\Models\HostelRoom;
use App\Models\KaiChatMessage;
use App\Models\KaiChatSession;
use App\Models\LibraryLoan;
use App\Models\Major;
use App\Models\Program;
use App\Models\ResultBatch;
use App\Models\ResultBatchItem;
use App\Models\Room;
use App\Models\Semester;
use App\Models\StudentCourseResult;
use App\Models\StudentEnrollment;
use App\Models\StudentFee;
use App\Models\StudentMark;
use App\Models\StudentPayment;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\TeachingAssignment;
use App\Models\Timetable;
use App\Models\TimetableSlot;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DemoDataSeeder extends Seeder
{
    use WithoutModelEvents;

    private const PASSWORD = 'DemoPass123!';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new RuntimeException('DemoDataSeeder is for non-production environments only.');
        }

        $this->call(IamRolePermissionSeeder::class);

        DB::transaction(function (): void {
            $now = now();

            $superAdmin = $this->user('demo.admin@kyauksetu.test', 'Demo Super Admin', 'super_admin');
            $admissionOfficer = $this->user('demo.admissions@kyauksetu.test', 'Demo Admission Officer', 'registrar');
            $teacherUser = $this->user('demo.teacher@kyauksetu.test', 'Daw Thandar Win', 'teacher');
            $studentUser = $this->user('demo.student@kyauksetu.test', 'Maya Hlaing', 'student');
            $applicantUser = $this->user('demo.applicant@kyauksetu.test', 'Maya Hlaing Applicant', 'applicant');

            $department = Department::updateOrCreate(
                ['code' => 'CSE-DEMO'],
                [
                    'name' => 'Computer Science and Engineering',
                    'description' => 'Demo department for the KAI MVP flow.',
                    'is_active' => true,
                ],
            );

            $program = Program::updateOrCreate(
                ['code' => 'BTECH-DEMO'],
                [
                    'name' => 'Bachelor of Technology',
                    'duration_years' => 4,
                    'status' => 'active',
                ],
            );

            $academicYear = AcademicYear::updateOrCreate(
                ['name' => '2026-2027 Demo'],
                [
                    'start_date' => '2026-06-01',
                    'end_date' => '2027-05-31',
                    'is_current' => true,
                    'status' => 'active',
                ],
            );

            $semester = Semester::updateOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'name' => 'Semester 1 Demo',
                ],
                [
                    'start_date' => '2026-06-01',
                    'end_date' => '2026-10-31',
                    'status' => 'active',
                ],
            );

            $major = Major::updateOrCreate(
                ['code' => 'CSE-DEMO'],
                [
                    'department_id' => $department->id,
                    'program_id' => $program->id,
                    'name' => 'Computer Science',
                    'status' => 'active',
                ],
            );

            $classSection = ClassSection::updateOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'major_id' => $major->id,
                    'year_level' => 1,
                    'section' => 'A',
                ],
                [
                    'name' => 'CSE First Year - Section A',
                    'status' => 'active',
                ],
            );

            $course = Course::updateOrCreate(
                ['code' => 'CSE-101-DEMO'],
                [
                    'name' => 'Programming Fundamentals',
                    'description' => 'Demo course used for attendance, marks, results, and KAI context.',
                    'credit_hours' => 3,
                    'lecture_hours' => 2,
                    'tutorial_hours' => 1,
                    'practical_hours' => 0,
                    'status' => 'active',
                ],
            );

            $curriculum = Curriculum::updateOrCreate(
                [
                    'program_id' => $program->id,
                    'major_id' => $major->id,
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                    'name' => 'CSE First Year Semester 1 Demo Curriculum',
                ],
                [
                    'year_level' => 1,
                    'status' => 'active',
                    'remarks' => 'Demo curriculum for the ERP/KAI MVP.',
                ],
            );

            $curriculumCourse = CurriculumCourse::updateOrCreate(
                [
                    'curriculum_id' => $curriculum->id,
                    'course_id' => $course->id,
                ],
                [
                    'is_required' => true,
                    'sort_order' => 1,
                    'remarks' => 'Core demo course.',
                ],
            );

            $teacherProfile = TeacherProfile::updateOrCreate(
                ['staff_no' => 'DEMO-T-001'],
                [
                    'user_id' => $teacherUser->id,
                    'department_id' => $department->id,
                    'institutional_email' => 'demo.teacher@kyauksetu.test',
                    'position' => 'Lecturer',
                    'rank' => 'Lecturer',
                    'status' => 'active',
                ],
            );

            $studentProfile = StudentProfile::updateOrCreate(
                ['student_no' => 'DEMO-STU-2026-0001'],
                [
                    'user_id' => $studentUser->id,
                    'department_id' => $department->id,
                    'program_id' => $program->id,
                    'major_id' => $major->id,
                    'academic_year_id' => $academicYear->id,
                    'class_section_id' => $classSection->id,
                    'first_name' => 'Maya',
                    'last_name' => 'Hlaing',
                    'date_of_birth' => '2008-04-12',
                    'phone' => '+959555010101',
                    'institutional_email' => 'demo.student@kyauksetu.test',
                    'roll_no' => 'CSE-1A-001',
                    'admission_year' => 2026,
                    'status' => 'active',
                    'enrolled_at' => '2026-06-03',
                ],
            );

            $studentEnrollment = StudentEnrollment::updateOrCreate(
                [
                    'student_profile_id' => $studentProfile->id,
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                ],
                [
                    'program_id' => $program->id,
                    'major_id' => $major->id,
                    'class_section_id' => $classSection->id,
                    'year_level' => 1,
                    'roll_no' => 'CSE-1A-001',
                    'status' => 'active',
                    'enrolled_at' => '2026-06-03',
                    'remarks' => 'Demo enrollment visible in student, teacher, and KAI contexts.',
                ],
            );

            $admissionBatch = AdmissionBatch::updateOrCreate(
                ['code' => 'DEMO-ADM-2026'],
                [
                    'academic_year_id' => $academicYear->id,
                    'program_id' => $program->id,
                    'name' => '2026 Demo Undergraduate Admissions',
                    'description' => 'Fake admissions batch for the MVP demo flow.',
                    'opens_at' => '2026-01-15',
                    'closes_at' => '2026-03-31',
                    'capacity' => 80,
                    'status' => 'open',
                    'remarks' => 'Demo data only.',
                ],
            );

            $applicant = Applicant::updateOrCreate(
                ['applicant_no' => 'DEMO-APP-2026-0001'],
                [
                    'user_id' => $applicantUser->id,
                    'first_name' => 'Maya',
                    'middle_name' => null,
                    'last_name' => 'Hlaing',
                    'email' => 'demo.applicant@kyauksetu.test',
                    'phone' => '+959555020202',
                    'date_of_birth' => '2008-04-12',
                    'gender' => 'female',
                    'national_id_no' => 'DEMO-NRC-000001',
                    'address' => 'Demo Address, Kyauksetu Township',
                    'status' => 'active',
                    'remarks' => 'Fake applicant record for demo only.',
                ],
            );

            $admissionApplication = AdmissionApplication::updateOrCreate(
                ['application_no' => 'DEMO-APP-CSE-2026-0001'],
                [
                    'admission_batch_id' => $admissionBatch->id,
                    'applicant_id' => $applicant->id,
                    'academic_year_id' => $academicYear->id,
                    'program_id' => $program->id,
                    'major_id' => $major->id,
                    'applied_at' => $now->copy()->subMonths(3),
                    'application_status' => 'accepted',
                    'student_profile_id' => $studentProfile->id,
                    'converted_by' => $admissionOfficer->id,
                    'converted_at' => $now->copy()->subMonths(1),
                    'remarks' => 'Accepted and linked to the demo student profile.',
                ],
            );

            AdmissionDecision::updateOrCreate(
                ['admission_application_id' => $admissionApplication->id],
                [
                    'decision_status' => 'accepted',
                    'decided_by' => $admissionOfficer->id,
                    'decided_at' => $now->copy()->subMonths(2),
                    'offer_expires_at' => $now->copy()->addMonth(),
                    'remarks' => 'Demo offer accepted.',
                ],
            );

            $building = Building::updateOrCreate(
                ['code' => 'DEMO-MAIN'],
                [
                    'name' => 'Demo Main Academic Building',
                    'description' => 'Demo campus building.',
                    'status' => 'active',
                ],
            );

            $room = Room::updateOrCreate(
                [
                    'building_id' => $building->id,
                    'code' => 'DEMO-LAB-101',
                ],
                [
                    'name' => 'Programming Lab 101',
                    'room_type' => 'lab',
                    'capacity' => 36,
                    'floor' => '1',
                    'status' => 'active',
                    'remarks' => 'Demo lab room.',
                ],
            );

            $teachingAssignment = TeachingAssignment::updateOrCreate(
                [
                    'teacher_profile_id' => $teacherProfile->id,
                    'course_id' => $course->id,
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                    'class_section_id' => $classSection->id,
                ],
                [
                    'program_id' => $program->id,
                    'major_id' => $major->id,
                    'curriculum_id' => $curriculum->id,
                    'curriculum_course_id' => $curriculumCourse->id,
                    'status' => 'active',
                    'starts_at' => '2026-06-01',
                    'ends_at' => '2026-10-31',
                    'remarks' => 'Demo teaching assignment.',
                ],
            );

            $timetable = Timetable::updateOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                    'class_section_id' => $classSection->id,
                    'name' => 'CSE First Year Demo Timetable',
                ],
                [
                    'program_id' => $program->id,
                    'major_id' => $major->id,
                    'effective_from' => '2026-06-01',
                    'effective_until' => '2026-10-31',
                    'status' => 'active',
                    'remarks' => 'Demo timetable for student and teacher portals.',
                ],
            );

            $timetableSlot = TimetableSlot::updateOrCreate(
                [
                    'timetable_id' => $timetable->id,
                    'teaching_assignment_id' => $teachingAssignment->id,
                    'day_of_week' => 'monday',
                    'starts_at' => '09:00:00',
                    'ends_at' => '10:30:00',
                ],
                [
                    'course_id' => $course->id,
                    'teacher_profile_id' => $teacherProfile->id,
                    'room_id' => $room->id,
                    'slot_type' => 'lecture',
                    'status' => 'scheduled',
                    'remarks' => 'Demo lecture slot.',
                ],
            );

            $attendanceSession = AttendanceSession::updateOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'class_section_id' => $classSection->id,
                    'course_id' => $course->id,
                    'session_date' => '2026-07-06',
                    'starts_at' => '09:00:00',
                    'ends_at' => '10:30:00',
                ],
                [
                    'semester_id' => $semester->id,
                    'teaching_assignment_id' => $teachingAssignment->id,
                    'timetable_slot_id' => $timetableSlot->id,
                    'teacher_profile_id' => $teacherProfile->id,
                    'room_id' => $room->id,
                    'status' => 'completed',
                    'remarks' => 'Demo attendance session.',
                ],
            );

            AttendanceRecord::updateOrCreate(
                [
                    'attendance_session_id' => $attendanceSession->id,
                    'student_enrollment_id' => $studentEnrollment->id,
                ],
                [
                    'status' => 'present',
                    'marked_at' => $now->copy()->subDays(2),
                    'marked_by' => $teacherUser->id,
                    'remarks' => 'Demo student attended the programming lecture.',
                ],
            );

            $assessmentComponent = AssessmentComponent::updateOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                    'class_section_id' => $classSection->id,
                    'course_id' => $course->id,
                    'name' => 'Programming Fundamentals Quiz 1',
                ],
                [
                    'component_type' => 'quiz',
                    'max_marks' => 20,
                    'weight' => 20,
                    'status' => 'published',
                    'remarks' => 'Demo assessment component.',
                ],
            );

            StudentMark::updateOrCreate(
                [
                    'assessment_component_id' => $assessmentComponent->id,
                    'student_enrollment_id' => $studentEnrollment->id,
                ],
                [
                    'marks_obtained' => 17,
                    'status' => 'approved',
                    'entered_by' => $teacherUser->id,
                    'entered_at' => $now->copy()->subDay(),
                    'approved_by' => $teacherUser->id,
                    'approved_at' => $now->copy()->subDay(),
                    'remarks' => 'Demo quiz mark.',
                ],
            );

            $gradeScale = GradeScale::updateOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'program_id' => $program->id,
                    'major_id' => $major->id,
                    'name' => 'Demo Undergraduate Grade Scale',
                ],
                [
                    'status' => 'active',
                    'remarks' => 'Demo grade scale.',
                ],
            );

            foreach ($this->gradeRules() as $rule) {
                GradeScaleRule::updateOrCreate(
                    [
                        'grade_scale_id' => $gradeScale->id,
                        'grade' => $rule['grade'],
                    ],
                    $rule,
                );
            }

            $studentCourseResult = StudentCourseResult::updateOrCreate(
                [
                    'student_enrollment_id' => $studentEnrollment->id,
                    'course_id' => $course->id,
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                ],
                [
                    'grade_scale_id' => $gradeScale->id,
                    'total_marks' => 85,
                    'percentage' => 85,
                    'grade' => 'A',
                    'grade_point' => 4,
                    'result_status' => 'published',
                    'calculated_by' => $teacherUser->id,
                    'calculated_at' => $now->copy()->subDay(),
                    'approved_by' => $superAdmin->id,
                    'approved_at' => $now->copy()->subHours(12),
                    'remarks' => 'Demo published course result.',
                ],
            );

            $resultBatch = ResultBatch::updateOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                    'class_section_id' => $classSection->id,
                    'name' => 'CSE First Year Demo Results',
                ],
                [
                    'program_id' => $program->id,
                    'major_id' => $major->id,
                    'status' => 'published',
                    'prepared_by' => $teacherUser->id,
                    'prepared_at' => $now->copy()->subDay(),
                    'approved_by' => $superAdmin->id,
                    'approved_at' => $now->copy()->subHours(12),
                    'published_at' => $now->copy()->subHours(6),
                    'remarks' => 'Demo result batch.',
                ],
            );

            ResultBatchItem::updateOrCreate(
                [
                    'result_batch_id' => $resultBatch->id,
                    'student_course_result_id' => $studentCourseResult->id,
                ],
                [
                    'student_enrollment_id' => $studentEnrollment->id,
                    'course_id' => $course->id,
                    'status' => 'included',
                    'remarks' => 'Demo result batch item.',
                ],
            );

            $feeType = FeeType::updateOrCreate(
                ['code' => 'DEMO-TUITION-S1'],
                [
                    'name' => 'Demo Semester Tuition',
                    'description' => 'Fake tuition fee for the demo student.',
                    'amount' => 250000,
                    'fee_category' => 'tuition',
                    'status' => 'active',
                    'remarks' => 'Demo only.',
                ],
            );

            $studentFee = StudentFee::updateOrCreate(
                [
                    'student_profile_id' => $studentProfile->id,
                    'student_enrollment_id' => $studentEnrollment->id,
                    'fee_type_id' => $feeType->id,
                ],
                [
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                    'amount' => 250000,
                    'discount_amount' => 25000,
                    'payable_amount' => 225000,
                    'due_at' => '2026-07-31',
                    'fee_status' => 'partial',
                    'remarks' => 'Demo fee with partial payment.',
                ],
            );

            StudentPayment::updateOrCreate(
                [
                    'student_fee_id' => $studentFee->id,
                    'payment_reference' => 'DEMO-PAY-2026-0001',
                ],
                [
                    'student_profile_id' => $studentProfile->id,
                    'amount' => 100000,
                    'payment_method' => 'cash',
                    'paid_at' => $now->copy()->subWeek(),
                    'payment_status' => 'completed',
                    'received_by' => $admissionOfficer->id,
                    'remarks' => 'Demo partial payment.',
                ],
            );

            $bookCategory = BookCategory::updateOrCreate(
                ['code' => 'DEMO-CS'],
                [
                    'name' => 'Demo Computer Science',
                    'description' => 'Demo library category.',
                    'status' => 'active',
                ],
            );

            $book = Book::updateOrCreate(
                ['isbn' => '9780000001011'],
                [
                    'book_category_id' => $bookCategory->id,
                    'title' => 'Programming Foundations for Beginners',
                    'subtitle' => 'Demo Edition',
                    'author' => 'KAI Demo Press',
                    'publisher' => 'Kyauksetu Demo Library',
                    'published_year' => 2026,
                    'edition' => '1st',
                    'description' => 'Fake demo library book.',
                    'status' => 'active',
                ],
            );

            $bookCopy = BookCopy::updateOrCreate(
                ['accession_no' => 'DEMO-ACC-0001'],
                [
                    'book_id' => $book->id,
                    'barcode' => 'DEMO-LIB-0001',
                    'copy_status' => 'on_loan',
                    'shelf_location' => 'CS-A1',
                    'acquired_at' => '2026-06-10',
                    'remarks' => 'Demo book copy.',
                ],
            );

            LibraryLoan::updateOrCreate(
                [
                    'book_copy_id' => $bookCopy->id,
                    'student_profile_id' => $studentProfile->id,
                    'loan_status' => 'active',
                ],
                [
                    'borrowed_at' => $now->copy()->subDays(5),
                    'due_at' => $now->copy()->addDays(9),
                    'returned_at' => null,
                    'issued_by' => $admissionOfficer->id,
                    'remarks' => 'Demo active student library loan.',
                ],
            );

            $hostel = Hostel::updateOrCreate(
                ['code' => 'DEMO-HOSTEL-A'],
                [
                    'name' => 'Demo Student Hostel A',
                    'description' => 'Fake hostel used for demo allocation.',
                    'gender_type' => 'female',
                    'status' => 'active',
                    'remarks' => 'Demo only.',
                ],
            );

            $hostelRoom = HostelRoom::updateOrCreate(
                [
                    'hostel_id' => $hostel->id,
                    'room_no' => 'A-101',
                ],
                [
                    'name' => 'Room A-101',
                    'floor' => '1',
                    'capacity' => 4,
                    'room_type' => 'shared',
                    'status' => 'active',
                    'remarks' => 'Demo hostel room.',
                ],
            );

            $hostelBed = HostelBed::updateOrCreate(
                [
                    'hostel_room_id' => $hostelRoom->id,
                    'bed_no' => 'A-101-1',
                ],
                [
                    'bed_status' => 'occupied',
                    'remarks' => 'Demo hostel bed.',
                ],
            );

            HostelAllocation::updateOrCreate(
                [
                    'student_profile_id' => $studentProfile->id,
                    'allocation_status' => 'active',
                ],
                [
                    'hostel_id' => $hostel->id,
                    'hostel_room_id' => $hostelRoom->id,
                    'hostel_bed_id' => $hostelBed->id,
                    'allocated_at' => $now->copy()->subWeeks(3),
                    'allocated_by' => $admissionOfficer->id,
                    'remarks' => 'Demo active hostel allocation.',
                ],
            );

            $announcement = Announcement::updateOrCreate(
                ['title' => 'Demo Orientation Week Schedule'],
                [
                    'body' => 'Orientation, library briefing, and KAI mobile walkthrough are scheduled for this week.',
                    'announcement_type' => 'academic',
                    'priority' => 'normal',
                    'status' => 'published',
                    'publish_at' => $now->copy()->subDay(),
                    'expires_at' => $now->copy()->addMonth(),
                    'created_by' => $superAdmin->id,
                    'remarks' => 'Demo announcement.',
                ],
            );

            AnnouncementAudience::updateOrCreate(
                [
                    'announcement_id' => $announcement->id,
                    'audience_type' => 'class_section',
                    'class_section_id' => $classSection->id,
                ],
                [
                    'role_name' => null,
                    'department_id' => $department->id,
                    'program_id' => $program->id,
                    'major_id' => $major->id,
                    'user_id' => null,
                    'remarks' => 'Demo class audience.',
                ],
            );

            $teacherAnnouncement = Announcement::updateOrCreate(
                ['title' => 'Demo Marks Entry Reminder'],
                [
                    'body' => 'Please review demo quiz marks and attendance before the published result walkthrough.',
                    'announcement_type' => 'academic',
                    'priority' => 'high',
                    'status' => 'published',
                    'publish_at' => $now->copy()->subDay(),
                    'expires_at' => $now->copy()->addMonth(),
                    'created_by' => $superAdmin->id,
                    'remarks' => 'Demo teacher announcement.',
                ],
            );

            AnnouncementAudience::updateOrCreate(
                [
                    'announcement_id' => $teacherAnnouncement->id,
                    'audience_type' => 'role',
                    'role_name' => 'teacher',
                ],
                [
                    'department_id' => $department->id,
                    'program_id' => null,
                    'major_id' => null,
                    'class_section_id' => null,
                    'user_id' => null,
                    'remarks' => 'Demo teacher audience.',
                ],
            );

            $kaiSession = KaiChatSession::updateOrCreate(
                [
                    'user_id' => $studentUser->id,
                    'title' => 'Demo Student KAI Check-in',
                ],
                [
                    'driver' => 'local',
                    'provider' => 'demo',
                    'model' => 'local-demo',
                    'status' => 'active',
                    'last_message_at' => $now,
                    'metadata' => ['demo_key' => 'student-kai-check-in'],
                ],
            );

            KaiChatMessage::updateOrCreate(
                [
                    'kai_chat_session_id' => $kaiSession->id,
                    'role' => 'user',
                    'content' => 'What should I focus on this week?',
                ],
                [
                    'user_id' => $studentUser->id,
                    'context_keys' => ['current_enrollment', 'today_upcoming_timetable', 'attendance', 'latest_results', 'unpaid_due_fees'],
                    'driver' => 'local',
                    'provider' => 'demo',
                    'model' => 'local-demo',
                    'status' => 'completed',
                    'metadata' => ['demo_key' => 'student-kai-question'],
                ],
            );

            KaiChatMessage::updateOrCreate(
                [
                    'kai_chat_session_id' => $kaiSession->id,
                    'role' => 'assistant',
                    'content' => 'Focus on Programming Fundamentals, keep your attendance streak, and check the remaining tuition balance before the due date.',
                ],
                [
                    'user_id' => $studentUser->id,
                    'context_keys' => ['current_enrollment', 'attendance', 'latest_results', 'unpaid_due_fees'],
                    'driver' => 'local',
                    'provider' => 'demo',
                    'model' => 'local-demo',
                    'status' => 'completed',
                    'metadata' => ['demo_key' => 'student-kai-answer'],
                ],
            );
        });

        $this->command?->info('Demo ERP/KAI seed data is ready.');
        $this->command?->table(
            ['Account', 'Email', 'Password'],
            [
                ['Super admin', 'demo.admin@kyauksetu.test', self::PASSWORD],
                ['Admission officer', 'demo.admissions@kyauksetu.test', self::PASSWORD],
                ['Teacher', 'demo.teacher@kyauksetu.test', self::PASSWORD],
                ['Student', 'demo.student@kyauksetu.test', self::PASSWORD],
                ['Applicant', 'demo.applicant@kyauksetu.test', self::PASSWORD],
            ],
        );
    }

    private function user(string $email, string $name, string $role): User
    {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'email_verified_at' => now(),
                'password' => self::PASSWORD,
            ],
        );

        $user->syncRoles([$role]);

        return $user;
    }

    /**
     * @return array<int, array{grade: string, min_marks: int, max_marks: int, grade_point: float, is_passing: bool, remarks: string}>
     */
    private function gradeRules(): array
    {
        return [
            ['grade' => 'A', 'min_marks' => 80, 'max_marks' => 100, 'grade_point' => 4.0, 'is_passing' => true, 'remarks' => 'Demo excellent range.'],
            ['grade' => 'B', 'min_marks' => 65, 'max_marks' => 79, 'grade_point' => 3.0, 'is_passing' => true, 'remarks' => 'Demo good range.'],
            ['grade' => 'C', 'min_marks' => 50, 'max_marks' => 64, 'grade_point' => 2.0, 'is_passing' => true, 'remarks' => 'Demo pass range.'],
            ['grade' => 'F', 'min_marks' => 0, 'max_marks' => 49, 'grade_point' => 0.0, 'is_passing' => false, 'remarks' => 'Demo fail range.'],
        ];
    }
}
