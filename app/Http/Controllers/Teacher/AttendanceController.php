<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreAttendanceSessionRequest;
use App\Http\Requests\Teacher\UpdateAttendanceRecordsRequest;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\StudentEnrollment;
use App\Models\TeacherProfile;
use App\Models\TeachingAssignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $profile = $this->currentTeacherProfile($request);

        return view('teacher.attendance.index', [
            'profile' => $profile,
            'assignments' => $this->assignmentQuery($profile)
                ->latest('starts_at')
                ->latest()
                ->get(),
            'sessions' => $this->sessionQuery($profile)
                ->with(['teachingAssignment.course', 'classSection', 'course'])
                ->latest('session_date')
                ->latest()
                ->get(),
        ]);
    }

    public function store(StoreAttendanceSessionRequest $request): RedirectResponse
    {
        $profile = $this->currentTeacherProfile($request);
        $validated = $request->validated();

        $assignment = $this->assignmentQuery($profile)
            ->findOrFail($validated['teaching_assignment_id']);

        $session = DB::transaction(function () use ($assignment, $profile, $request, $validated): AttendanceSession {
            $session = AttendanceSession::firstOrCreate(
                [
                    'academic_year_id' => $assignment->academic_year_id,
                    'semester_id' => $assignment->semester_id,
                    'class_section_id' => $assignment->class_section_id,
                    'teaching_assignment_id' => $assignment->id,
                    'course_id' => $assignment->course_id,
                    'teacher_profile_id' => $profile->id,
                    'session_date' => $validated['session_date'],
                    'starts_at' => $validated['starts_at'] ?? null,
                    'ends_at' => $validated['ends_at'] ?? null,
                ],
                [
                    'status' => 'draft',
                    'remarks' => $validated['remarks'] ?? null,
                ],
            );

            $this->ensureRecordsForSession($session, $request->user()->id);

            return $session;
        });

        return redirect()
            ->route('teacher.attendance.sessions.show', $session)
            ->with('status', 'Attendance session ready.');
    }

    public function show(Request $request, AttendanceSession $session): View
    {
        $profile = $this->currentTeacherProfile($request);
        $this->abortUnlessOwnSession($profile, $session);

        return view('teacher.attendance.show', [
            'profile' => $profile,
            'session' => $session->load([
                'teachingAssignment.course',
                'academicYear',
                'semester',
                'classSection',
                'course',
                'records.studentEnrollment.studentProfile.user',
            ]),
            'statuses' => ['present', 'absent', 'late', 'excused'],
        ]);
    }

    public function updateRecords(UpdateAttendanceRecordsRequest $request, AttendanceSession $session): RedirectResponse
    {
        $profile = $this->currentTeacherProfile($request);
        $this->abortUnlessOwnSession($profile, $session);

        $records = $request->validated()['records'];
        $recordIds = collect(array_keys($records))->map(fn (string|int $id): int => (int) $id);
        $ownedRecordIds = $session
            ->records()
            ->whereIn('id', $recordIds)
            ->pluck('id')
            ->sort()
            ->values();

        abort_unless($ownedRecordIds->all() === $recordIds->sort()->values()->all(), 403);

        DB::transaction(function () use ($records, $request): void {
            foreach ($records as $recordId => $record) {
                AttendanceRecord::query()
                    ->whereKey($recordId)
                    ->update([
                        'status' => $record['status'],
                        'remarks' => $record['remarks'] ?? null,
                        'marked_at' => now(),
                        'marked_by' => $request->user()->id,
                    ]);
            }
        });

        return redirect()
            ->route('teacher.attendance.sessions.show', $session)
            ->with('status', 'Attendance records updated.');
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
            ->whereNotNull('class_section_id')
            ->with(['academicYear', 'semester', 'classSection', 'course']);
    }

    private function sessionQuery(TeacherProfile $profile): Builder
    {
        return AttendanceSession::query()
            ->where(function (Builder $query) use ($profile): void {
                $query
                    ->whereBelongsTo($profile)
                    ->orWhereHas('teachingAssignment', fn (Builder $query) => $query->whereBelongsTo($profile));
            });
    }

    private function abortUnlessOwnSession(TeacherProfile $profile, AttendanceSession $session): void
    {
        abort_unless(
            $this->sessionQuery($profile)->whereKey($session->id)->exists(),
            403,
        );
    }

    private function ensureRecordsForSession(AttendanceSession $session, int $markedBy): void
    {
        StudentEnrollment::query()
            ->where('class_section_id', $session->class_section_id)
            ->with('studentProfile')
            ->orderBy('roll_no')
            ->get()
            ->each(function (StudentEnrollment $enrollment) use ($session, $markedBy): void {
                AttendanceRecord::firstOrCreate(
                    [
                        'attendance_session_id' => $session->id,
                        'student_enrollment_id' => $enrollment->id,
                    ],
                    [
                        'status' => 'present',
                        'marked_at' => now(),
                        'marked_by' => $markedBy,
                    ],
                );
            });
    }
}
