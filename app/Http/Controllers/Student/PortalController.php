<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AttendanceRecord;
use App\Models\HostelAllocation;
use App\Models\LibraryLoan;
use App\Models\StudentCourseResult;
use App\Models\StudentEnrollment;
use App\Models\StudentFee;
use App\Models\StudentProfile;
use App\Models\Timetable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function dashboard(Request $request): View
    {
        $profile = $this->currentStudentProfile($request);
        $currentEnrollment = $this->currentEnrollment($profile);

        return view('student.dashboard', [
            'profile' => $profile,
            'currentEnrollment' => $currentEnrollment,
            'feeCount' => $profile->studentFees()->count(),
            'resultCount' => $this->studentResults($profile)->count(),
            'announcementCount' => $this->studentAnnouncements($profile, $request)->count(),
        ]);
    }

    public function profile(Request $request): View
    {
        return view('student.profile', [
            'profile' => $this->currentStudentProfile($request),
        ]);
    }

    public function enrollment(Request $request): View
    {
        $profile = $this->currentStudentProfile($request);

        return view('student.enrollment', [
            'profile' => $profile,
            'enrollments' => $profile->studentEnrollments()
                ->with(['academicYear', 'semester', 'program', 'major', 'classSection'])
                ->latest('enrolled_at')
                ->latest()
                ->get(),
        ]);
    }

    public function timetable(Request $request): View
    {
        $profile = $this->currentStudentProfile($request);
        $currentEnrollment = $this->currentEnrollment($profile);

        return view('student.timetable', [
            'profile' => $profile,
            'currentEnrollment' => $currentEnrollment,
            'timetables' => $currentEnrollment
                ? $this->studentTimetables($currentEnrollment)
                : collect(),
        ]);
    }

    public function attendance(Request $request): View
    {
        $profile = $this->currentStudentProfile($request);

        return view('student.attendance', [
            'profile' => $profile,
            'records' => AttendanceRecord::query()
                ->whereIn('student_enrollment_id', $this->studentEnrollmentIds($profile))
                ->with(['attendanceSession.course', 'attendanceSession.teacherProfile.user', 'attendanceSession.room', 'studentEnrollment'])
                ->latest('marked_at')
                ->latest()
                ->get(),
        ]);
    }

    public function results(Request $request): View
    {
        $profile = $this->currentStudentProfile($request);

        return view('student.results', [
            'profile' => $profile,
            'results' => $this->studentResults($profile)
                ->with(['academicYear', 'semester', 'course', 'gradeScale', 'studentEnrollment'])
                ->latest('approved_at')
                ->latest()
                ->get(),
        ]);
    }

    public function fees(Request $request): View
    {
        $profile = $this->currentStudentProfile($request);

        return view('student.fees', [
            'profile' => $profile,
            'fees' => StudentFee::query()
                ->whereBelongsTo($profile)
                ->with(['academicYear', 'semester', 'feeType', 'studentPayments'])
                ->latest('due_at')
                ->latest()
                ->get(),
        ]);
    }

    public function library(Request $request): View
    {
        $profile = $this->currentStudentProfile($request);

        return view('student.library', [
            'profile' => $profile,
            'loans' => LibraryLoan::query()
                ->whereBelongsTo($profile)
                ->with(['bookCopy.book'])
                ->latest('borrowed_at')
                ->latest()
                ->get(),
        ]);
    }

    public function hostel(Request $request): View
    {
        $profile = $this->currentStudentProfile($request);

        return view('student.hostel', [
            'profile' => $profile,
            'allocations' => HostelAllocation::query()
                ->whereBelongsTo($profile)
                ->with(['hostel', 'hostelRoom', 'hostelBed'])
                ->latest('allocated_at')
                ->latest()
                ->get(),
        ]);
    }

    public function announcements(Request $request): View
    {
        $profile = $this->currentStudentProfile($request);

        return view('student.announcements', [
            'profile' => $profile,
            'announcements' => $this->studentAnnouncements($profile, $request)
                ->with('audiences')
                ->latest('publish_at')
                ->latest()
                ->get(),
        ]);
    }

    private function currentStudentProfile(Request $request): StudentProfile
    {
        return $request->user()
            ->studentProfile()
            ->with(['academicYear', 'classSection', 'department', 'major', 'program', 'user'])
            ->firstOrFail();
    }

    private function currentEnrollment(StudentProfile $profile): ?StudentEnrollment
    {
        return $profile->studentEnrollments()
            ->with(['academicYear', 'semester', 'program', 'major', 'classSection'])
            ->where('status', 'active')
            ->latest('enrolled_at')
            ->latest()
            ->first();
    }

    /**
     * @return Collection<int, Timetable>
     */
    private function studentTimetables(StudentEnrollment $enrollment): Collection
    {
        return Timetable::query()
            ->where('academic_year_id', $enrollment->academic_year_id)
            ->when($enrollment->semester_id, fn ($query) => $query->where('semester_id', $enrollment->semester_id))
            ->when(
                $enrollment->class_section_id,
                fn ($query) => $query->where('class_section_id', $enrollment->class_section_id),
                fn ($query) => $query
                    ->where('program_id', $enrollment->program_id)
                    ->where('major_id', $enrollment->major_id),
            )
            ->with(['academicYear', 'semester', 'program', 'major', 'classSection', 'slots.course', 'slots.teacherProfile.user', 'slots.room'])
            ->orderBy('effective_from')
            ->get();
    }

    /**
     * @return array<int, int>
     */
    private function studentEnrollmentIds(StudentProfile $profile): array
    {
        return $profile->studentEnrollments()->pluck('id')->all();
    }

    private function studentResults(StudentProfile $profile): Builder
    {
        return StudentCourseResult::query()
            ->whereIn('student_enrollment_id', $this->studentEnrollmentIds($profile));
    }

    private function studentAnnouncements(StudentProfile $profile, Request $request): Builder
    {
        return Announcement::query()
            ->where('status', 'published')
            ->where(function ($query): void {
                $query
                    ->whereNull('publish_at')
                    ->orWhere('publish_at', '<=', now());
            })
            ->where(function ($query): void {
                $query
                    ->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->whereHas('audiences', function ($query) use ($profile, $request): void {
                $query
                    ->where('audience_type', 'all')
                    ->orWhere(function ($query): void {
                        $query
                            ->where('audience_type', 'role')
                            ->where('role_name', 'student');
                    })
                    ->orWhere(function ($query) use ($request): void {
                        $query
                            ->where('audience_type', 'user')
                            ->where('user_id', $request->user()->id);
                    });

                if ($profile->department_id) {
                    $query->orWhere(function ($query) use ($profile): void {
                        $query
                            ->where('audience_type', 'department')
                            ->where('department_id', $profile->department_id);
                    });
                }

                if ($profile->program_id) {
                    $query->orWhere(function ($query) use ($profile): void {
                        $query
                            ->where('audience_type', 'program')
                            ->where('program_id', $profile->program_id);
                    });
                }

                if ($profile->major_id) {
                    $query->orWhere(function ($query) use ($profile): void {
                        $query
                            ->where('audience_type', 'major')
                            ->where('major_id', $profile->major_id);
                    });
                }

                if ($profile->class_section_id) {
                    $query->orWhere(function ($query) use ($profile): void {
                        $query
                            ->where('audience_type', 'class_section')
                            ->where('class_section_id', $profile->class_section_id);
                    });
                }
            });
    }
}
