<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Building;
use App\Models\ClassSection;
use App\Models\Course;
use App\Models\Curriculum;
use App\Models\CurriculumCourse;
use App\Models\Department;
use App\Models\Major;
use App\Models\Program;
use App\Models\Room;
use App\Models\Semester;
use App\Models\StudentEnrollment;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\TeachingAssignment;
use App\Models\Timetable;
use App\Models\TimetableSlot;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class SmokeTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new RuntimeException('SmokeTestSeeder is for non-production environments only.');
        }

        $records = DB::transaction(function (): array {
            $department = Department::updateOrCreate(
                ['code' => 'IT'],
                [
                    'name' => 'Information Technology',
                    'description' => 'Smoke test department.',
                    'is_active' => true,
                ],
            );

            $program = Program::updateOrCreate(
                ['code' => 'BE'],
                [
                    'name' => 'Bachelor of Engineering',
                    'duration_years' => 4,
                    'status' => 'active',
                ],
            );

            $academicYear = AcademicYear::updateOrCreate(
                ['name' => '2026-2027'],
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
                    'name' => 'Semester 1',
                ],
                [
                    'start_date' => '2026-06-01',
                    'end_date' => '2026-10-31',
                    'status' => 'active',
                ],
            );

            $major = Major::updateOrCreate(
                ['code' => 'IT'],
                [
                    'department_id' => $department->id,
                    'program_id' => $program->id,
                    'name' => 'Information Technology',
                    'status' => 'active',
                ],
            );

            $classSection = ClassSection::updateOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'major_id' => $major->id,
                    'year_level' => 3,
                    'section' => 'A',
                ],
                [
                    'name' => 'IT Third Year - Section A',
                    'status' => 'active',
                ],
            );

            $course = Course::updateOrCreate(
                ['code' => 'IT-3101'],
                [
                    'name' => 'Database Systems',
                    'description' => 'Smoke test course.',
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
                    'name' => 'IT Third Year Semester 1 Curriculum',
                ],
                [
                    'year_level' => 3,
                    'status' => 'active',
                    'remarks' => 'Smoke test curriculum.',
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
                    'remarks' => 'Smoke test curriculum course.',
                ],
            );

            $teacherUser = User::updateOrCreate(
                ['email' => 'teacher@ktu.test'],
                [
                    'name' => 'Smoke Test Teacher',
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                ],
            );

            $teacherProfile = TeacherProfile::updateOrCreate(
                ['staff_no' => 'T-001'],
                [
                    'user_id' => $teacherUser->id,
                    'department_id' => $department->id,
                    'first_name' => 'Smoke',
                    'last_name' => 'Teacher',
                    'institutional_email' => 'teacher@ktu.test',
                    'position' => 'Lecturer',
                    'rank' => 'Lecturer',
                    'hire_date' => '2026-06-01',
                    'status' => 'active',
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
                    'remarks' => 'Smoke test teaching assignment.',
                ],
            );

            $building = Building::updateOrCreate(
                ['code' => 'MAIN'],
                [
                    'name' => 'Main Building',
                    'description' => 'Smoke test building.',
                    'status' => 'active',
                ],
            );

            $room = Room::updateOrCreate(
                [
                    'building_id' => $building->id,
                    'code' => 'R-101',
                ],
                [
                    'name' => 'Room 101',
                    'room_type' => 'classroom',
                    'capacity' => 40,
                    'floor' => '1',
                    'status' => 'active',
                    'remarks' => 'Smoke test room.',
                ],
            );

            $timetable = Timetable::updateOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                    'class_section_id' => $classSection->id,
                    'name' => 'IT Third Year Semester 1 Timetable',
                ],
                [
                    'program_id' => $program->id,
                    'major_id' => $major->id,
                    'effective_from' => '2026-06-01',
                    'effective_until' => '2026-10-31',
                    'status' => 'active',
                    'remarks' => 'Smoke test timetable.',
                ],
            );

            $timetableSlot = TimetableSlot::updateOrCreate(
                [
                    'timetable_id' => $timetable->id,
                    'teaching_assignment_id' => $teachingAssignment->id,
                    'course_id' => $course->id,
                    'teacher_profile_id' => $teacherProfile->id,
                    'room_id' => $room->id,
                    'day_of_week' => 'monday',
                    'starts_at' => '09:00:00',
                    'ends_at' => '10:30:00',
                    'slot_type' => 'lecture',
                ],
                [
                    'status' => 'scheduled',
                    'remarks' => 'Smoke test timetable slot.',
                ],
            );

            $studentUser = User::updateOrCreate(
                ['email' => 'student@ktu.test'],
                [
                    'name' => 'Smoke Test Student',
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                ],
            );

            $studentProfile = StudentProfile::updateOrCreate(
                ['student_no' => 'S-001'],
                [
                    'user_id' => $studentUser->id,
                    'department_id' => $department->id,
                    'program_id' => $program->id,
                    'major_id' => $major->id,
                    'academic_year_id' => $academicYear->id,
                    'class_section_id' => $classSection->id,
                    'first_name' => 'Smoke',
                    'last_name' => 'Student',
                    'institutional_email' => 'student@ktu.test',
                    'roll_no' => 'IT-3A-001',
                    'admission_year' => 2026,
                    'status' => 'active',
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
                    'year_level' => 3,
                    'roll_no' => 'IT-3A-001',
                    'status' => 'active',
                    'enrolled_at' => '2026-06-01',
                    'remarks' => 'Smoke test enrollment.',
                ],
            );

            $this->verifyRelationships(
                department: $department,
                program: $program,
                academicYear: $academicYear,
                semester: $semester,
                major: $major,
                classSection: $classSection,
                course: $course,
                curriculum: $curriculum,
                teacherProfile: $teacherProfile,
                teachingAssignment: $teachingAssignment,
                building: $building,
                room: $room,
                timetable: $timetable,
                timetableSlot: $timetableSlot,
                studentProfile: $studentProfile,
                studentEnrollment: $studentEnrollment,
            );

            return [
                'Department' => $department,
                'Program' => $program,
                'Academic Year' => $academicYear,
                'Semester' => $semester,
                'Major' => $major,
                'Class Section' => $classSection,
                'Course' => $course,
                'Curriculum' => $curriculum,
                'Teacher Profile' => $teacherProfile,
                'Teaching Assignment' => $teachingAssignment,
                'Building' => $building,
                'Room' => $room,
                'Timetable' => $timetable,
                'Timetable Slot' => $timetableSlot,
                'Student Profile' => $studentProfile,
                'Student Enrollment' => $studentEnrollment,
            ];
        });

        $this->command?->info('Smoke test foundation data created and verified successfully.');
        $this->command?->table(
            ['Record', 'ID'],
            collect($records)
                ->map(fn (object $record, string $label): array => [$label, (string) $record->getKey()])
                ->values()
                ->all(),
        );
    }

    private function verifyRelationships(
        Department $department,
        Program $program,
        AcademicYear $academicYear,
        Semester $semester,
        Major $major,
        ClassSection $classSection,
        Course $course,
        Curriculum $curriculum,
        TeacherProfile $teacherProfile,
        TeachingAssignment $teachingAssignment,
        Building $building,
        Room $room,
        Timetable $timetable,
        TimetableSlot $timetableSlot,
        StudentProfile $studentProfile,
        StudentEnrollment $studentEnrollment,
    ): void {
        $this->assertRelationship($major->department()->is($department), 'Major does not belong to Department.');
        $this->assertRelationship($major->program()->is($program), 'Major does not belong to Program.');
        $this->assertRelationship($classSection->academicYear()->is($academicYear), 'ClassSection does not belong to AcademicYear.');
        $this->assertRelationship($classSection->major()->is($major), 'ClassSection does not belong to Major.');
        $this->assertRelationship($curriculum->curriculumCourses()->exists(), 'Curriculum has no CurriculumCourse records.');
        $this->assertRelationship($teachingAssignment->teacherProfile()->is($teacherProfile), 'TeachingAssignment does not belong to TeacherProfile.');
        $this->assertRelationship($teachingAssignment->course()->is($course), 'TeachingAssignment does not belong to Course.');
        $this->assertRelationship($teachingAssignment->academicYear()->is($academicYear), 'TeachingAssignment does not belong to AcademicYear.');
        $this->assertRelationship($teachingAssignment->semester()->is($semester), 'TeachingAssignment does not belong to Semester.');
        $this->assertRelationship($teachingAssignment->classSection()->is($classSection), 'TeachingAssignment does not belong to ClassSection.');
        $this->assertRelationship($room->building()->is($building), 'Room does not belong to Building.');
        $this->assertRelationship($timetable->slots()->exists(), 'Timetable has no TimetableSlot records.');
        $this->assertRelationship($timetableSlot->course()->is($course), 'TimetableSlot does not belong to Course.');
        $this->assertRelationship($timetableSlot->teacherProfile()->is($teacherProfile), 'TimetableSlot does not belong to TeacherProfile.');
        $this->assertRelationship($timetableSlot->room()->is($room), 'TimetableSlot does not belong to Room.');
        $this->assertRelationship($timetableSlot->teachingAssignment()->is($teachingAssignment), 'TimetableSlot does not belong to TeachingAssignment.');
        $this->assertRelationship($studentEnrollment->studentProfile()->is($studentProfile), 'StudentEnrollment does not belong to StudentProfile.');
        $this->assertRelationship($studentEnrollment->academicYear()->is($academicYear), 'StudentEnrollment does not belong to AcademicYear.');
        $this->assertRelationship($studentEnrollment->semester()->is($semester), 'StudentEnrollment does not belong to Semester.');
        $this->assertRelationship($studentEnrollment->program()->is($program), 'StudentEnrollment does not belong to Program.');
        $this->assertRelationship($studentEnrollment->major()->is($major), 'StudentEnrollment does not belong to Major.');
        $this->assertRelationship($studentEnrollment->classSection()->is($classSection), 'StudentEnrollment does not belong to ClassSection.');
    }

    private function assertRelationship(bool $condition, string $message): void
    {
        if (! $condition) {
            throw new RuntimeException($message);
        }
    }
}
