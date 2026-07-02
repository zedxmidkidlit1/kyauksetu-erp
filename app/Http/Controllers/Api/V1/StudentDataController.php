<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\AnnouncementResource;
use App\Http\Resources\Api\V1\AttendanceRecordResource;
use App\Http\Resources\Api\V1\CurrentUserResource;
use App\Http\Resources\Api\V1\HostelAllocationResource;
use App\Http\Resources\Api\V1\LibraryLoanResource;
use App\Http\Resources\Api\V1\StudentCourseResultResource;
use App\Http\Resources\Api\V1\StudentEnrollmentResource;
use App\Http\Resources\Api\V1\StudentFeeResource;
use App\Http\Resources\Api\V1\StudentProfileResource;
use App\Http\Resources\Api\V1\TimetableResource;
use App\Models\Announcement;
use App\Models\AttendanceRecord;
use App\Models\HostelAllocation;
use App\Models\LibraryLoan;
use App\Models\StudentCourseResult;
use App\Models\StudentEnrollment;
use App\Models\StudentFee;
use App\Models\StudentProfile;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StudentDataController extends Controller
{
    public function me(Request $request): CurrentUserResource
    {
        return new CurrentUserResource($request->user());
    }

    public function myProfile(Request $request): StudentProfileResource
    {
        return new StudentProfileResource($this->currentStudentProfile($request));
    }

    public function myEnrollment(Request $request): StudentEnrollmentResource
    {
        return new StudentEnrollmentResource(
            $this->currentEnrollment($this->currentStudentProfile($request)),
        );
    }

    public function myTimetable(Request $request): AnonymousResourceCollection
    {
        $currentEnrollment = $this->currentEnrollment($this->currentStudentProfile($request));

        return TimetableResource::collection(
            $currentEnrollment ? $this->studentTimetables($currentEnrollment) : collect(),
        );
    }

    public function myAttendance(Request $request): AnonymousResourceCollection
    {
        $profile = $this->currentStudentProfile($request);

        return AttendanceRecordResource::collection(
            AttendanceRecord::query()
                ->whereIn('student_enrollment_id', $this->studentEnrollmentIds($profile))
                ->with(['attendanceSession.course', 'attendanceSession.teacherProfile.user', 'attendanceSession.room', 'studentEnrollment'])
                ->latest('marked_at')
                ->latest()
                ->get(),
        );
    }

    public function myResults(Request $request): AnonymousResourceCollection
    {
        $profile = $this->currentStudentProfile($request);

        return StudentCourseResultResource::collection(
            $this->studentResults($profile)
                ->with(['academicYear', 'semester', 'course', 'gradeScale', 'studentEnrollment'])
                ->latest('approved_at')
                ->latest()
                ->get(),
        );
    }

    public function myFees(Request $request): AnonymousResourceCollection
    {
        $profile = $this->currentStudentProfile($request);

        return StudentFeeResource::collection(
            StudentFee::query()
                ->whereBelongsTo($profile)
                ->with(['academicYear', 'semester', 'feeType', 'studentPayments'])
                ->latest('due_at')
                ->latest()
                ->get(),
        );
    }

    public function myLibrary(Request $request): AnonymousResourceCollection
    {
        $profile = $this->currentStudentProfile($request);

        return LibraryLoanResource::collection(
            LibraryLoan::query()
                ->whereBelongsTo($profile)
                ->with(['bookCopy.book'])
                ->latest('borrowed_at')
                ->latest()
                ->get(),
        );
    }

    public function myHostel(Request $request): AnonymousResourceCollection
    {
        $profile = $this->currentStudentProfile($request);

        return HostelAllocationResource::collection(
            HostelAllocation::query()
                ->whereBelongsTo($profile)
                ->with(['hostel', 'hostelRoom', 'hostelBed'])
                ->latest('allocated_at')
                ->latest()
                ->get(),
        );
    }

    public function announcements(Request $request): AnonymousResourceCollection
    {
        $profile = $this->currentStudentProfile($request);

        return AnnouncementResource::collection(
            $this->studentAnnouncements($profile, $request)
                ->latest('publish_at')
                ->latest()
                ->get(),
        );
    }

    private function currentStudentProfile(Request $request): StudentProfile
    {
        $user = $request->user();

        abort_unless($user instanceof User, 401);

        $profile = $user
            ->studentProfile()
            ->with(['academicYear', 'classSection', 'department', 'major', 'program', 'user'])
            ->first();

        abort_unless($profile instanceof StudentProfile, 403, 'This account is not linked to a student profile.');

        return $profile;
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
            ->when($enrollment->semester_id, fn (Builder $query) => $query->where('semester_id', $enrollment->semester_id))
            ->when(
                $enrollment->class_section_id,
                fn (Builder $query) => $query->where('class_section_id', $enrollment->class_section_id),
                fn (Builder $query) => $query
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
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('publish_at')
                    ->orWhere('publish_at', '<=', now());
            })
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->whereHas('audiences', function (Builder $query) use ($profile, $request): void {
                $query
                    ->where('audience_type', 'all')
                    ->orWhere(function (Builder $query): void {
                        $query
                            ->where('audience_type', 'role')
                            ->where('role_name', 'student');
                    })
                    ->orWhere(function (Builder $query) use ($request): void {
                        $query
                            ->where('audience_type', 'user')
                            ->where('user_id', $request->user()?->id);
                    });

                if ($profile->department_id) {
                    $query->orWhere(function (Builder $query) use ($profile): void {
                        $query
                            ->where('audience_type', 'department')
                            ->where('department_id', $profile->department_id);
                    });
                }

                if ($profile->program_id) {
                    $query->orWhere(function (Builder $query) use ($profile): void {
                        $query
                            ->where('audience_type', 'program')
                            ->where('program_id', $profile->program_id);
                    });
                }

                if ($profile->major_id) {
                    $query->orWhere(function (Builder $query) use ($profile): void {
                        $query
                            ->where('audience_type', 'major')
                            ->where('major_id', $profile->major_id);
                    });
                }

                if ($profile->class_section_id) {
                    $query->orWhere(function (Builder $query) use ($profile): void {
                        $query
                            ->where('audience_type', 'class_section')
                            ->where('class_section_id', $profile->class_section_id);
                    });
                }
            });
    }
}
