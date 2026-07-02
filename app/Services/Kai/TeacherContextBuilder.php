<?php

namespace App\Services\Kai;

use App\Models\Announcement;
use App\Models\AssessmentComponent;
use App\Models\AttendanceSession;
use App\Models\StudentEnrollment;
use App\Models\TeacherProfile;
use App\Models\TeachingAssignment;
use App\Models\TimetableSlot;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TeacherContextBuilder
{
    /**
     * @return array<string, mixed>
     */
    public function buildFor(User $user): array
    {
        abort_unless($user->hasRole('teacher'), 403, 'KAI context is only available for teacher accounts.');

        $profile = $this->currentTeacherProfile($user);
        $assignments = $this->assignmentQuery($profile)->get();
        $assignmentIds = $assignments->pluck('id')->all();
        $classSectionIds = $assignments->pluck('class_section_id')->filter()->unique()->values()->all();

        return [
            'generated_at' => now()->toJSON(),
            'user' => $this->userSummary($user),
            'teacher_profile' => $this->profileSummary($profile),
            'teaching_assignments' => $this->assignmentSummary($assignments),
            'today_upcoming_timetable' => $this->timetableSummary($profile, $assignmentIds),
            'assigned_classes' => $this->classSummary($assignments, $classSectionIds),
            'recent_attendance_sessions' => $this->attendanceSessionSummary($profile),
            'assessment_components_pending_marks' => $this->assessmentComponentSummary($assignments, $classSectionIds),
            'visible_announcements' => $this->announcementSummary($profile, $assignments, $user),
        ];
    }

    private function currentTeacherProfile(User $user): TeacherProfile
    {
        $profile = $user
            ->teacherProfile()
            ->with(['department', 'user'])
            ->first();

        abort_unless($profile instanceof TeacherProfile, 403, 'This account is not linked to a teacher profile.');

        return $profile;
    }

    private function assignmentQuery(TeacherProfile $profile): Builder
    {
        return TeachingAssignment::query()
            ->whereBelongsTo($profile)
            ->with(['academicYear', 'semester', 'program', 'major', 'classSection', 'course'])
            ->latest('starts_at')
            ->latest();
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
    private function profileSummary(TeacherProfile $profile): array
    {
        return [
            'id' => $profile->id,
            'staff_no' => $profile->staff_no,
            'institutional_email' => $profile->institutional_email,
            'position' => $profile->position,
            'rank' => $profile->rank,
            'status' => $profile->status,
            'department' => $profile->department ? [
                'id' => $profile->department->id,
                'name' => $profile->department->name,
                'code' => $profile->department->code,
            ] : null,
        ];
    }

    /**
     * @param  Collection<int, TeachingAssignment>  $assignments
     * @return array<string, mixed>
     */
    private function assignmentSummary(Collection $assignments): array
    {
        return [
            'count' => $assignments->count(),
            'items' => $assignments->take(8)->map(fn (TeachingAssignment $assignment): array => [
                'id' => $assignment->id,
                'status' => $assignment->status,
                'academic_year' => $assignment->academicYear ? [
                    'id' => $assignment->academicYear->id,
                    'name' => $assignment->academicYear->name,
                ] : null,
                'semester' => $assignment->semester ? [
                    'id' => $assignment->semester->id,
                    'name' => $assignment->semester->name,
                ] : null,
                'course' => $assignment->course ? [
                    'id' => $assignment->course->id,
                    'name' => $assignment->course->name,
                    'code' => $assignment->course->code,
                ] : null,
                'class_section' => $assignment->classSection ? [
                    'id' => $assignment->classSection->id,
                    'name' => $assignment->classSection->name,
                    'section' => $assignment->classSection->section,
                ] : null,
            ])->values()->all(),
        ];
    }

    /**
     * @param  array<int, int>  $assignmentIds
     * @return array<string, mixed>
     */
    private function timetableSummary(TeacherProfile $profile, array $assignmentIds): array
    {
        $dayOrder = $this->upcomingDayOrder();

        $slots = TimetableSlot::query()
            ->where(function (Builder $query) use ($profile, $assignmentIds): void {
                $query->whereBelongsTo($profile);

                if ($assignmentIds !== []) {
                    $query->orWhereIn('teaching_assignment_id', $assignmentIds);
                }
            })
            ->with(['course', 'room', 'timetable.classSection'])
            ->get()
            ->filter(fn (TimetableSlot $slot): bool => isset($dayOrder[$slot->day_of_week]))
            ->sortBy([
                fn (TimetableSlot $slot): int => $dayOrder[$slot->day_of_week],
                fn (TimetableSlot $slot): string => (string) $slot->starts_at,
            ])
            ->take(8)
            ->values();

        return [
            'today' => now()->format('l'),
            'items' => $slots->map(fn (TimetableSlot $slot): array => [
                'id' => $slot->id,
                'day_of_week' => $slot->day_of_week,
                'starts_at' => $slot->starts_at,
                'ends_at' => $slot->ends_at,
                'slot_type' => $slot->slot_type,
                'course' => $slot->course ? [
                    'id' => $slot->course->id,
                    'name' => $slot->course->name,
                    'code' => $slot->course->code,
                ] : null,
                'class_section' => $slot->timetable?->classSection ? [
                    'id' => $slot->timetable->classSection->id,
                    'name' => $slot->timetable->classSection->name,
                    'section' => $slot->timetable->classSection->section,
                ] : null,
                'room' => $slot->room ? [
                    'id' => $slot->room->id,
                    'name' => $slot->room->name,
                    'code' => $slot->room->code,
                ] : null,
            ])->all(),
        ];
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
     * @param  Collection<int, TeachingAssignment>  $assignments
     * @param  array<int, int>  $classSectionIds
     * @return array<string, mixed>
     */
    private function classSummary(Collection $assignments, array $classSectionIds): array
    {
        $studentCounts = StudentEnrollment::query()
            ->whereIn('class_section_id', $classSectionIds)
            ->selectRaw('class_section_id, count(*) as student_count')
            ->groupBy('class_section_id')
            ->pluck('student_count', 'class_section_id');
        $classes = $assignments
            ->filter(fn (TeachingAssignment $assignment): bool => (bool) $assignment->classSection)
            ->unique('class_section_id')
            ->values();

        return [
            'count' => $classes->count(),
            'student_count' => (int) $studentCounts->sum(),
            'items' => $classes->map(fn (TeachingAssignment $assignment): array => [
                'class_section' => [
                    'id' => $assignment->classSection->id,
                    'name' => $assignment->classSection->name,
                    'section' => $assignment->classSection->section,
                    'year_level' => $assignment->classSection->year_level,
                ],
                'student_count' => (int) ($studentCounts[$assignment->class_section_id] ?? 0),
            ])->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function attendanceSessionSummary(TeacherProfile $profile): array
    {
        $sessions = AttendanceSession::query()
            ->where(function (Builder $query) use ($profile): void {
                $query
                    ->whereBelongsTo($profile)
                    ->orWhereHas('teachingAssignment', fn (Builder $query) => $query->whereBelongsTo($profile));
            })
            ->with(['classSection', 'course'])
            ->withCount('records')
            ->latest('session_date')
            ->latest()
            ->limit(6)
            ->get();

        return [
            'count' => $sessions->count(),
            'items' => $sessions->map(fn (AttendanceSession $session): array => [
                'id' => $session->id,
                'session_date' => $session->session_date?->toDateString(),
                'status' => $session->status,
                'records_count' => $session->records_count,
                'course' => $session->course ? [
                    'id' => $session->course->id,
                    'name' => $session->course->name,
                    'code' => $session->course->code,
                ] : null,
                'class_section' => $session->classSection ? [
                    'id' => $session->classSection->id,
                    'name' => $session->classSection->name,
                    'section' => $session->classSection->section,
                ] : null,
            ])->all(),
        ];
    }

    /**
     * @param  Collection<int, TeachingAssignment>  $assignments
     * @param  array<int, int>  $classSectionIds
     * @return array<string, mixed>
     */
    private function assessmentComponentSummary(Collection $assignments, array $classSectionIds): array
    {
        $components = $this->assessmentComponentQuery($assignments)
            ->with(['course', 'classSection'])
            ->withCount('studentMarks')
            ->latest()
            ->limit(8)
            ->get();
        $studentCounts = StudentEnrollment::query()
            ->whereIn('class_section_id', $classSectionIds)
            ->selectRaw('class_section_id, count(*) as student_count')
            ->groupBy('class_section_id')
            ->pluck('student_count', 'class_section_id');

        return [
            'count' => $components->count(),
            'pending_count' => $components->sum(function (AssessmentComponent $component) use ($studentCounts): int {
                $studentCount = (int) ($studentCounts[$component->class_section_id] ?? 0);

                return max(0, $studentCount - (int) $component->student_marks_count);
            }),
            'items' => $components->map(function (AssessmentComponent $component) use ($studentCounts): array {
                $studentCount = (int) ($studentCounts[$component->class_section_id] ?? 0);

                return [
                    'id' => $component->id,
                    'name' => $component->name,
                    'component_type' => $component->component_type,
                    'max_marks' => $component->max_marks,
                    'status' => $component->status,
                    'student_count' => $studentCount,
                    'marks_entered_count' => $component->student_marks_count,
                    'pending_count' => max(0, $studentCount - (int) $component->student_marks_count),
                    'course' => $component->course ? [
                        'id' => $component->course->id,
                        'name' => $component->course->name,
                        'code' => $component->course->code,
                    ] : null,
                    'class_section' => $component->classSection ? [
                        'id' => $component->classSection->id,
                        'name' => $component->classSection->name,
                        'section' => $component->classSection->section,
                    ] : null,
                ];
            })->all(),
        ];
    }

    /**
     * @param  Collection<int, TeachingAssignment>  $assignments
     */
    private function assessmentComponentQuery(Collection $assignments): Builder
    {
        return AssessmentComponent::query()
            ->where(function (Builder $query) use ($assignments): void {
                if ($assignments->isEmpty()) {
                    $query->whereRaw('1 = 0');

                    return;
                }

                $assignments->each(function (TeachingAssignment $assignment) use ($query): void {
                    $query->orWhere(function (Builder $query) use ($assignment): void {
                        $query
                            ->where('academic_year_id', $assignment->academic_year_id)
                            ->where('semester_id', $assignment->semester_id)
                            ->where('class_section_id', $assignment->class_section_id)
                            ->where('course_id', $assignment->course_id);
                    });
                });
            });
    }

    /**
     * @param  Collection<int, TeachingAssignment>  $assignments
     * @return array<string, mixed>
     */
    private function announcementSummary(TeacherProfile $profile, Collection $assignments, User $user): array
    {
        $announcements = $this->teacherAnnouncements($profile, $assignments, $user)
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

    /**
     * @param  Collection<int, TeachingAssignment>  $assignments
     */
    private function teacherAnnouncements(TeacherProfile $profile, Collection $assignments, User $user): Builder
    {
        $programIds = $assignments->pluck('program_id')->filter()->unique()->values()->all();
        $majorIds = $assignments->pluck('major_id')->filter()->unique()->values()->all();
        $classSectionIds = $assignments->pluck('class_section_id')->filter()->unique()->values()->all();

        return Announcement::query()
            ->where('status', 'published')
            ->where(function (Builder $query): void {
                $query->whereNull('publish_at')->orWhere('publish_at', '<=', now());
            })
            ->where(function (Builder $query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->whereHas('audiences', function (Builder $query) use ($profile, $user, $programIds, $majorIds, $classSectionIds): void {
                $query
                    ->where('audience_type', 'all')
                    ->orWhere(fn (Builder $query) => $query->where('audience_type', 'role')->where('role_name', 'teacher'))
                    ->orWhere(fn (Builder $query) => $query->where('audience_type', 'user')->where('user_id', $user->id));

                if ($profile->department_id) {
                    $query->orWhere(fn (Builder $query) => $query->where('audience_type', 'department')->where('department_id', $profile->department_id));
                }

                if ($programIds !== []) {
                    $query->orWhere(fn (Builder $query) => $query->where('audience_type', 'program')->whereIn('program_id', $programIds));
                }

                if ($majorIds !== []) {
                    $query->orWhere(fn (Builder $query) => $query->where('audience_type', 'major')->whereIn('major_id', $majorIds));
                }

                if ($classSectionIds !== []) {
                    $query->orWhere(fn (Builder $query) => $query->where('audience_type', 'class_section')->whereIn('class_section_id', $classSectionIds));
                }
            });
    }
}
