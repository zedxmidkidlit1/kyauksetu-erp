<?php

namespace App\Services\Kai;

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
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class StudentContextBuilder
{
    /**
     * @return array<string, mixed>
     */
    public function buildFor(User $user): array
    {
        abort_unless($user->hasRole('student'), 403, 'KAI context is only available for student accounts.');

        $profile = $this->currentStudentProfile($user);
        $currentEnrollment = $this->currentEnrollment($profile);
        $enrollmentIds = $this->studentEnrollmentIds($profile);

        return [
            'generated_at' => now()->toJSON(),
            'user' => $this->userSummary($user),
            'student_profile' => $this->profileSummary($profile),
            'current_enrollment' => $currentEnrollment ? $this->enrollmentSummary($currentEnrollment) : null,
            'today_upcoming_timetable' => $currentEnrollment
                ? $this->timetableSummary($currentEnrollment)
                : ['today' => now()->format('l'), 'items' => []],
            'visible_announcements' => $this->announcementSummary($profile, $user),
            'attendance' => $this->attendanceSummary($enrollmentIds),
            'latest_results' => $this->latestResultsSummary($enrollmentIds),
            'unpaid_due_fees' => $this->feeSummary($profile),
            'active_library_loans' => $this->libraryLoanSummary($profile),
            'active_hostel_allocation' => $this->hostelAllocationSummary($profile),
        ];
    }

    private function currentStudentProfile(User $user): StudentProfile
    {
        $profile = $user
            ->studentProfile()
            ->with(['academicYear', 'classSection', 'department', 'major', 'program'])
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
     * @return array<int, int>
     */
    private function studentEnrollmentIds(StudentProfile $profile): array
    {
        return $profile->studentEnrollments()->pluck('id')->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function userSummary(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function profileSummary(StudentProfile $profile): array
    {
        return [
            'id' => $profile->id,
            'student_no' => $profile->student_no,
            'roll_no' => $profile->roll_no,
            'name' => trim("{$profile->first_name} {$profile->last_name}"),
            'institutional_email' => $profile->institutional_email,
            'status' => $profile->status,
            'program' => $profile->program ? [
                'id' => $profile->program->id,
                'name' => $profile->program->name,
                'code' => $profile->program->code,
            ] : null,
            'major' => $profile->major ? [
                'id' => $profile->major->id,
                'name' => $profile->major->name,
                'code' => $profile->major->code,
            ] : null,
            'class_section' => $profile->classSection ? [
                'id' => $profile->classSection->id,
                'name' => $profile->classSection->name,
                'section' => $profile->classSection->section,
            ] : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function enrollmentSummary(StudentEnrollment $enrollment): array
    {
        return [
            'id' => $enrollment->id,
            'roll_no' => $enrollment->roll_no,
            'year_level' => $enrollment->year_level,
            'status' => $enrollment->status,
            'enrolled_at' => $enrollment->enrolled_at?->toDateString(),
            'academic_year' => $enrollment->academicYear ? [
                'id' => $enrollment->academicYear->id,
                'name' => $enrollment->academicYear->name,
            ] : null,
            'semester' => $enrollment->semester ? [
                'id' => $enrollment->semester->id,
                'name' => $enrollment->semester->name,
            ] : null,
            'class_section' => $enrollment->classSection ? [
                'id' => $enrollment->classSection->id,
                'name' => $enrollment->classSection->name,
                'section' => $enrollment->classSection->section,
            ] : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function timetableSummary(StudentEnrollment $enrollment): array
    {
        $dayOrder = $this->upcomingDayOrder();

        $items = $this->studentTimetables($enrollment)
            ->flatMap(fn (Timetable $timetable): Collection => $timetable->slots->map(fn ($slot): array => [
                'timetable_id' => $timetable->id,
                'timetable_name' => $timetable->name,
                'day_of_week' => $slot->day_of_week,
                'starts_at' => $slot->starts_at,
                'ends_at' => $slot->ends_at,
                'slot_type' => $slot->slot_type,
                'course' => $slot->course ? [
                    'id' => $slot->course->id,
                    'name' => $slot->course->name,
                    'code' => $slot->course->code,
                ] : null,
                'room' => $slot->room ? [
                    'id' => $slot->room->id,
                    'name' => $slot->room->name,
                    'code' => $slot->room->code,
                ] : null,
            ]))
            ->filter(fn (array $slot): bool => isset($dayOrder[$slot['day_of_week']]))
            ->sortBy([
                fn (array $slot): int => $dayOrder[$slot['day_of_week']],
                fn (array $slot): string => $slot['starts_at'],
            ])
            ->take(8)
            ->values();

        return [
            'today' => now()->format('l'),
            'items' => $items->all(),
        ];
    }

    /**
     * @return EloquentCollection<int, Timetable>
     */
    private function studentTimetables(StudentEnrollment $enrollment): EloquentCollection
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
            ->with(['slots.course', 'slots.room'])
            ->orderBy('effective_from')
            ->get();
    }

    /**
     * @return array<string, int>
     */
    private function upcomingDayOrder(): array
    {
        return collect(range(0, 6))
            ->mapWithKeys(fn (int $offset): array => [
                strtolower(now()->copy()->addDays($offset)->format('l')) => $offset,
            ])
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function announcementSummary(StudentProfile $profile, User $user): array
    {
        $announcements = $this->studentAnnouncements($profile, $user)
            ->latest('publish_at')
            ->latest()
            ->limit(5)
            ->get();

        return [
            'count' => $announcements->count(),
            'items' => $announcements->map(fn (Announcement $announcement): array => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'priority' => $announcement->priority,
                'type' => $announcement->announcement_type,
                'publish_at' => $announcement->publish_at?->toJSON(),
                'expires_at' => $announcement->expires_at?->toJSON(),
            ])->all(),
        ];
    }

    private function studentAnnouncements(StudentProfile $profile, User $user): Builder
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
            ->whereHas('audiences', function (Builder $query) use ($profile, $user): void {
                $query
                    ->where('audience_type', 'all')
                    ->orWhere(function (Builder $query): void {
                        $query
                            ->where('audience_type', 'role')
                            ->where('role_name', 'student');
                    })
                    ->orWhere(function (Builder $query) use ($user): void {
                        $query
                            ->where('audience_type', 'user')
                            ->where('user_id', $user->id);
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

    /**
     * @param  array<int, int>  $enrollmentIds
     * @return array<string, mixed>
     */
    private function attendanceSummary(array $enrollmentIds): array
    {
        $records = AttendanceRecord::query()
            ->whereIn('student_enrollment_id', $enrollmentIds)
            ->with(['attendanceSession.course'])
            ->latest('marked_at')
            ->latest()
            ->limit(20)
            ->get();

        return [
            'total_recent' => $records->count(),
            'present_count' => $records->where('status', 'present')->count(),
            'absent_count' => $records->where('status', 'absent')->count(),
            'latest' => $records->take(5)->map(fn (AttendanceRecord $record): array => [
                'id' => $record->id,
                'status' => $record->status,
                'marked_at' => $record->marked_at?->toJSON(),
                'course' => $record->attendanceSession?->course ? [
                    'id' => $record->attendanceSession->course->id,
                    'name' => $record->attendanceSession->course->name,
                    'code' => $record->attendanceSession->course->code,
                ] : null,
            ])->values()->all(),
        ];
    }

    /**
     * @param  array<int, int>  $enrollmentIds
     * @return array<string, mixed>
     */
    private function latestResultsSummary(array $enrollmentIds): array
    {
        $results = StudentCourseResult::query()
            ->whereIn('student_enrollment_id', $enrollmentIds)
            ->with(['course', 'semester'])
            ->latest('approved_at')
            ->latest()
            ->limit(5)
            ->get();

        return [
            'count' => $results->count(),
            'items' => $results->map(fn (StudentCourseResult $result): array => [
                'id' => $result->id,
                'grade' => $result->grade,
                'grade_point' => $result->grade_point,
                'result_status' => $result->result_status,
                'course' => $result->course ? [
                    'id' => $result->course->id,
                    'name' => $result->course->name,
                    'code' => $result->course->code,
                ] : null,
                'semester' => $result->semester ? [
                    'id' => $result->semester->id,
                    'name' => $result->semester->name,
                ] : null,
            ])->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function feeSummary(StudentProfile $profile): array
    {
        $fees = StudentFee::query()
            ->whereBelongsTo($profile)
            ->whereNotIn('fee_status', ['paid', 'waived'])
            ->with(['feeType'])
            ->orderBy('due_at')
            ->limit(5)
            ->get();

        return [
            'count' => $fees->count(),
            'total_payable_amount' => number_format((float) $fees->sum('payable_amount'), 2, '.', ''),
            'items' => $fees->map(fn (StudentFee $fee): array => [
                'id' => $fee->id,
                'payable_amount' => $fee->payable_amount,
                'due_at' => $fee->due_at?->toDateString(),
                'fee_status' => $fee->fee_status,
                'fee_type' => $fee->feeType ? [
                    'id' => $fee->feeType->id,
                    'name' => $fee->feeType->name,
                    'code' => $fee->feeType->code,
                ] : null,
            ])->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function libraryLoanSummary(StudentProfile $profile): array
    {
        $loans = LibraryLoan::query()
            ->whereBelongsTo($profile)
            ->where('loan_status', 'active')
            ->with(['bookCopy.book'])
            ->orderBy('due_at')
            ->limit(5)
            ->get();

        return [
            'count' => $loans->count(),
            'items' => $loans->map(fn (LibraryLoan $loan): array => [
                'id' => $loan->id,
                'due_at' => $loan->due_at?->toJSON(),
                'loan_status' => $loan->loan_status,
                'book' => $loan->bookCopy?->book ? [
                    'id' => $loan->bookCopy->book->id,
                    'title' => $loan->bookCopy->book->title,
                    'author' => $loan->bookCopy->book->author,
                ] : null,
            ])->all(),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function hostelAllocationSummary(StudentProfile $profile): ?array
    {
        $allocation = HostelAllocation::query()
            ->whereBelongsTo($profile)
            ->where('allocation_status', 'active')
            ->with(['hostel', 'hostelRoom', 'hostelBed'])
            ->latest('allocated_at')
            ->latest()
            ->first();

        if (! $allocation instanceof HostelAllocation) {
            return null;
        }

        return [
            'id' => $allocation->id,
            'allocated_at' => $allocation->allocated_at?->toJSON(),
            'hostel' => $allocation->hostel ? [
                'id' => $allocation->hostel->id,
                'name' => $allocation->hostel->name,
                'code' => $allocation->hostel->code,
            ] : null,
            'room' => $allocation->hostelRoom ? [
                'id' => $allocation->hostelRoom->id,
                'room_no' => $allocation->hostelRoom->room_no,
            ] : null,
            'bed' => $allocation->hostelBed ? [
                'id' => $allocation->hostelBed->id,
                'bed_no' => $allocation->hostelBed->bed_no,
            ] : null,
        ];
    }
}
