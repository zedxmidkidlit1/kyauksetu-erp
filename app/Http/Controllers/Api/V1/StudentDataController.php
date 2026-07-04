<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StudentDataQueryRequest;
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
use App\Models\AttendanceRecord;
use App\Models\HostelAllocation;
use App\Models\LibraryLoan;
use App\Models\StudentCourseResult;
use App\Models\StudentEnrollment;
use App\Models\StudentFee;
use App\Models\StudentProfile;
use App\Models\Timetable;
use App\Models\User;
use App\Services\Mobile\VisibleAnnouncementQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function myTimetable(StudentDataQueryRequest $request): AnonymousResourceCollection
    {
        $currentEnrollment = $this->currentEnrollment($this->currentStudentProfile($request));

        return TimetableResource::collection(
            $currentEnrollment
                ? $this->applyDateRange($this->studentTimetables($currentEnrollment), $request, 'effective_from')->paginate($request->perPage())
                : $this->emptyPaginator($request),
        );
    }

    public function myAttendance(StudentDataQueryRequest $request): AnonymousResourceCollection
    {
        $profile = $this->currentStudentProfile($request);

        return AttendanceRecordResource::collection(
            $this->applyDateRange(AttendanceRecord::query()
                ->whereIn('student_enrollment_id', $this->studentEnrollmentIds($profile))
                ->with(['attendanceSession.course', 'attendanceSession.teacherProfile.user', 'attendanceSession.room', 'studentEnrollment'])
                ->latest('marked_at')
                ->latest(), $request, 'marked_at')
                ->paginate($request->perPage()),
        );
    }

    public function myResults(StudentDataQueryRequest $request): AnonymousResourceCollection
    {
        $profile = $this->currentStudentProfile($request);

        return StudentCourseResultResource::collection(
            $this->applyDateRange($this->studentResults($profile)
                ->with(['academicYear', 'semester', 'course', 'gradeScale', 'studentEnrollment'])
                ->latest('approved_at')
                ->latest(), $request, 'approved_at')
                ->paginate($request->perPage()),
        );
    }

    public function myFees(StudentDataQueryRequest $request): AnonymousResourceCollection
    {
        $profile = $this->currentStudentProfile($request);

        return StudentFeeResource::collection(
            $this->applyDateRange(StudentFee::query()
                ->whereBelongsTo($profile)
                ->with(['academicYear', 'semester', 'feeType', 'studentPayments'])
                ->latest('due_at')
                ->latest(), $request, 'due_at')
                ->paginate($request->perPage()),
        );
    }

    public function myLibrary(StudentDataQueryRequest $request): AnonymousResourceCollection
    {
        $profile = $this->currentStudentProfile($request);

        return LibraryLoanResource::collection(
            $this->applyDateRange(LibraryLoan::query()
                ->whereBelongsTo($profile)
                ->with(['bookCopy.book'])
                ->latest('borrowed_at')
                ->latest(), $request, 'borrowed_at')
                ->paginate($request->perPage()),
        );
    }

    public function myHostel(StudentDataQueryRequest $request): AnonymousResourceCollection
    {
        $profile = $this->currentStudentProfile($request);

        return HostelAllocationResource::collection(
            $this->applyDateRange(HostelAllocation::query()
                ->whereBelongsTo($profile)
                ->with(['hostel', 'hostelRoom', 'hostelBed'])
                ->latest('allocated_at')
                ->latest(), $request, 'allocated_at')
                ->paginate($request->perPage()),
        );
    }

    public function announcements(StudentDataQueryRequest $request, VisibleAnnouncementQuery $announcements): AnonymousResourceCollection
    {
        $profile = $this->currentStudentProfile($request);
        $user = $profile->user;

        return AnnouncementResource::collection(
            $this->applyDateRange($announcements->forUser($user)
                ->latest('publish_at')
                ->latest(), $request, 'publish_at')
                ->paginate($request->perPage()),
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

    private function studentTimetables(StudentEnrollment $enrollment): Builder
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
            ->orderBy('effective_from');
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

    private function applyDateRange(Builder $query, StudentDataQueryRequest $request, string $column): Builder
    {
        if ($request->filled('from')) {
            $query->whereDate($column, '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate($column, '<=', $request->date('to'));
        }

        return $query;
    }

    private function emptyPaginator(StudentDataQueryRequest $request): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            items: [],
            total: 0,
            perPage: $request->perPage(),
            currentPage: $request->integer('page', 1),
            options: [
                'path' => $request->url(),
                'query' => $request->query(),
            ],
        );
    }
}
