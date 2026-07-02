<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\UpdateStudentMarksRequest;
use App\Models\AssessmentComponent;
use App\Models\StudentEnrollment;
use App\Models\StudentMark;
use App\Models\TeacherProfile;
use App\Models\TeachingAssignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MarksController extends Controller
{
    public function index(Request $request): View
    {
        $profile = $this->currentTeacherProfile($request);

        return view('teacher.marks.index', [
            'profile' => $profile,
            'assessmentComponents' => $this->componentQuery($profile)
                ->with(['academicYear', 'semester', 'classSection', 'course'])
                ->withCount('studentMarks')
                ->latest()
                ->get(),
        ]);
    }

    public function show(Request $request, AssessmentComponent $component): View
    {
        $profile = $this->currentTeacherProfile($request);
        $this->abortUnlessOwnComponent($profile, $component);

        $students = $this->studentsForComponent($component)
            ->with(['studentProfile.user', 'program', 'major', 'classSection'])
            ->orderBy('roll_no')
            ->get();
        $marksByEnrollment = StudentMark::query()
            ->whereBelongsTo($component)
            ->whereIn('student_enrollment_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_enrollment_id');

        return view('teacher.marks.show', [
            'profile' => $profile,
            'assessmentComponent' => $component->load(['academicYear', 'semester', 'classSection', 'course']),
            'students' => $students,
            'marksByEnrollment' => $marksByEnrollment,
        ]);
    }

    public function updateStudents(UpdateStudentMarksRequest $request, AssessmentComponent $component): RedirectResponse
    {
        $profile = $this->currentTeacherProfile($request);
        $this->abortUnlessOwnComponent($profile, $component);

        $records = $request->validated()['records'];
        $submittedEnrollmentIds = collect(array_keys($records))->map(fn (string|int $id): int => (int) $id);
        $allowedEnrollmentIds = $this->studentsForComponent($component)
            ->whereIn('id', $submittedEnrollmentIds)
            ->pluck('id')
            ->sort()
            ->values();

        abort_unless($allowedEnrollmentIds->all() === $submittedEnrollmentIds->sort()->values()->all(), 403);

        DB::transaction(function () use ($component, $records, $request): void {
            foreach ($records as $enrollmentId => $record) {
                $marks = $record['marks_obtained'] ?? null;

                StudentMark::updateOrCreate(
                    [
                        'assessment_component_id' => $component->id,
                        'student_enrollment_id' => $enrollmentId,
                    ],
                    [
                        'marks_obtained' => $marks === '' ? null : $marks,
                        'status' => 'draft',
                        'entered_by' => $request->user()->id,
                        'entered_at' => now(),
                        'remarks' => $record['remarks'] ?? null,
                    ],
                );
            }
        });

        return redirect()
            ->route('teacher.marks.components.show', $component)
            ->with('status', 'Student marks saved.');
    }

    private function currentTeacherProfile(Request $request): TeacherProfile
    {
        return $request->user()
            ->teacherProfile()
            ->with(['department', 'user'])
            ->firstOrFail();
    }

    private function componentQuery(TeacherProfile $profile): Builder
    {
        $assignments = TeachingAssignment::query()
            ->whereBelongsTo($profile)
            ->whereNotNull('class_section_id')
            ->whereNotNull('course_id')
            ->get(['academic_year_id', 'semester_id', 'class_section_id', 'course_id']);

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

    private function abortUnlessOwnComponent(TeacherProfile $profile, AssessmentComponent $component): void
    {
        abort_unless(
            $this->componentQuery($profile)->whereKey($component->id)->exists(),
            403,
        );
    }

    private function studentsForComponent(AssessmentComponent $component): Builder
    {
        return StudentEnrollment::query()
            ->where('academic_year_id', $component->academic_year_id)
            ->where('semester_id', $component->semester_id)
            ->where('class_section_id', $component->class_section_id);
    }
}
