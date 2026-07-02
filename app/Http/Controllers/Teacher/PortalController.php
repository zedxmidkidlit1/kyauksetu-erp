<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\StudentEnrollment;
use App\Models\TeacherProfile;
use App\Models\TeachingAssignment;
use App\Models\Timetable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function dashboard(Request $request): View
    {
        $profile = $this->currentTeacherProfile($request);
        $assignments = $this->assignmentQuery($profile)->get();

        return view('teacher.dashboard', [
            'profile' => $profile,
            'assignmentCount' => $assignments->count(),
            'classCount' => $assignments->pluck('class_section_id')->filter()->unique()->count(),
            'studentCount' => $this->students($profile)->count(),
            'announcementCount' => $this->announcementsFor($profile, $request)->count(),
        ]);
    }

    public function profile(Request $request): View
    {
        return view('teacher.profile', [
            'profile' => $this->currentTeacherProfile($request),
        ]);
    }

    public function assignments(Request $request): View
    {
        $profile = $this->currentTeacherProfile($request);

        return view('teacher.assignments', [
            'profile' => $profile,
            'assignments' => $this->assignmentQuery($profile)
                ->latest('starts_at')
                ->latest()
                ->get(),
        ]);
    }

    public function timetable(Request $request): View
    {
        $profile = $this->currentTeacherProfile($request);

        return view('teacher.timetable', [
            'profile' => $profile,
            'timetables' => $this->timetables($profile),
        ]);
    }

    public function classes(Request $request): View
    {
        $profile = $this->currentTeacherProfile($request);

        return view('teacher.classes', [
            'profile' => $profile,
            'assignments' => $this->assignmentQuery($profile)->get(),
            'students' => $this->students($profile)
                ->with(['academicYear', 'semester', 'classSection', 'program', 'major', 'studentProfile.user'])
                ->orderBy('roll_no')
                ->get(),
        ]);
    }

    public function announcements(Request $request): View
    {
        $profile = $this->currentTeacherProfile($request);

        return view('teacher.announcements', [
            'profile' => $profile,
            'announcements' => $this->announcementsFor($profile, $request)
                ->with('audiences')
                ->latest('publish_at')
                ->latest()
                ->get(),
        ]);
    }

    private function currentTeacherProfile(Request $request): TeacherProfile
    {
        return $request->user()
            ->teacherProfile()
            ->with(['department', 'user'])
            ->firstOrFail();
    }

    private function assignmentQuery(TeacherProfile $profile): Builder
    {
        return TeachingAssignment::query()
            ->whereBelongsTo($profile)
            ->with(['academicYear', 'semester', 'program', 'major', 'classSection', 'course']);
    }

    /**
     * @return EloquentCollection<int, Timetable>
     */
    private function timetables(TeacherProfile $profile): EloquentCollection
    {
        $assignmentIds = $this->assignmentIds($profile);

        return Timetable::query()
            ->whereHas('slots', function (Builder $query) use ($profile, $assignmentIds): void {
                $query->where('teacher_profile_id', $profile->id);

                if ($assignmentIds !== []) {
                    $query->orWhereIn('teaching_assignment_id', $assignmentIds);
                }
            })
            ->with([
                'academicYear',
                'semester',
                'program',
                'major',
                'classSection',
                'slots' => function ($query) use ($profile, $assignmentIds): void {
                    $query
                        ->where('teacher_profile_id', $profile->id)
                        ->when($assignmentIds !== [], fn ($query) => $query->orWhereIn('teaching_assignment_id', $assignmentIds))
                        ->with(['course', 'room', 'teachingAssignment'])
                        ->orderBy('day_of_week')
                        ->orderBy('starts_at');
                },
            ])
            ->orderBy('effective_from')
            ->get();
    }

    private function students(TeacherProfile $profile): Builder
    {
        return StudentEnrollment::query()
            ->whereIn('class_section_id', $this->classSectionIds($profile));
    }

    /**
     * @return array<int, int>
     */
    private function assignmentIds(TeacherProfile $profile): array
    {
        return $profile->teachingAssignments()->pluck('id')->all();
    }

    /**
     * @return array<int, int>
     */
    private function classSectionIds(TeacherProfile $profile): array
    {
        return $profile
            ->teachingAssignments()
            ->whereNotNull('class_section_id')
            ->pluck('class_section_id')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int, int>
     */
    private function assignedIds(TeacherProfile $profile, string $column): array
    {
        return $profile
            ->teachingAssignments()
            ->whereNotNull($column)
            ->pluck($column)
            ->unique()
            ->values()
            ->all();
    }

    private function announcementsFor(TeacherProfile $profile, Request $request): Builder
    {
        $departmentId = $profile->department_id;
        $programIds = $this->assignedIds($profile, 'program_id');
        $majorIds = $this->assignedIds($profile, 'major_id');
        $classSectionIds = $this->classSectionIds($profile);

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
            ->whereHas('audiences', function (Builder $query) use ($request, $departmentId, $programIds, $majorIds, $classSectionIds): void {
                $query
                    ->where('audience_type', 'all')
                    ->orWhere(function (Builder $query): void {
                        $query
                            ->where('audience_type', 'role')
                            ->where('role_name', 'teacher');
                    })
                    ->orWhere(function (Builder $query) use ($request): void {
                        $query
                            ->where('audience_type', 'user')
                            ->where('user_id', $request->user()->id);
                    });

                if ($departmentId) {
                    $query->orWhere(function (Builder $query) use ($departmentId): void {
                        $query
                            ->where('audience_type', 'department')
                            ->where('department_id', $departmentId);
                    });
                }

                if ($programIds !== []) {
                    $query->orWhere(function (Builder $query) use ($programIds): void {
                        $query
                            ->where('audience_type', 'program')
                            ->whereIn('program_id', $programIds);
                    });
                }

                if ($majorIds !== []) {
                    $query->orWhere(function (Builder $query) use ($majorIds): void {
                        $query
                            ->where('audience_type', 'major')
                            ->whereIn('major_id', $majorIds);
                    });
                }

                if ($classSectionIds !== []) {
                    $query->orWhere(function (Builder $query) use ($classSectionIds): void {
                        $query
                            ->where('audience_type', 'class_section')
                            ->whereIn('class_section_id', $classSectionIds);
                    });
                }
            });
    }
}
