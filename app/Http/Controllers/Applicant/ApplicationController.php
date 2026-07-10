<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\StoreAdmissionApplicationRequest;
use App\Models\AdmissionApplication;
use App\Models\AdmissionBatch;
use App\Models\Applicant;
use App\Models\Major;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $applicant = $this->currentApplicant($request);

        return view('applicant.applications.index', [
            'applications' => AdmissionApplication::query()
                ->whereBelongsTo($applicant)
                ->with(['admissionBatch', 'program', 'admissionDecision'])
                ->latest()
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('applicant.applications.create', [
            'batches' => AdmissionBatch::query()
                ->acceptingApplications()
                ->with(['academicYear', 'program'])
                ->orderBy('closes_at')
                ->orderBy('name')
                ->get(),
            'programs' => Program::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),
            'majors' => Major::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreAdmissionApplicationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $applicantId = $this->currentApplicant($request)->getKey();

        $application = DB::transaction(function () use ($applicantId, $data): AdmissionApplication {
            $applicant = Applicant::query()
                ->lockForUpdate()
                ->findOrFail($applicantId);
            $batch = AdmissionBatch::query()
                ->acceptingApplications()
                ->lockForUpdate()
                ->findOrFail($data['admission_batch_id']);

            $major = isset($data['major_id'])
                ? Major::query()->findOrFail($data['major_id'])
                : null;
            $programId = $batch->program_id ?? $data['program_id'] ?? $major?->program_id;

            $alreadyApplied = AdmissionApplication::query()
                ->whereBelongsTo($applicant)
                ->whereBelongsTo($batch)
                ->when($programId, fn ($query) => $query->where('program_id', $programId))
                ->when(! $programId, fn ($query) => $query->whereNull('program_id'))
                ->exists();

            if ($alreadyApplied) {
                throw ValidationException::withMessages([
                    'admission_batch_id' => 'You already have an application for this batch and program.',
                ]);
            }

            return AdmissionApplication::create([
                'admission_batch_id' => $batch->id,
                'applicant_id' => $applicant->id,
                'academic_year_id' => $batch->academic_year_id,
                'program_id' => $programId,
                'major_id' => $data['major_id'] ?? null,
                'application_no' => $this->newApplicationNumber(),
                'applied_at' => now(),
                'application_status' => 'submitted',
                'remarks' => $data['remarks'] ?? null,
            ]);
        }, attempts: 3);

        return redirect()
            ->route('applicant.applications.status', $application)
            ->with('status', 'Application submitted.');
    }

    public function show(Request $request, AdmissionApplication $admissionApplication): View
    {
        $this->authorizeApplicantApplication($request, $admissionApplication);

        return view('applicant.applications.show', [
            'application' => $admissionApplication->load([
                'admissionBatch',
                'academicYear',
                'program',
                'major',
                'admissionDecision',
                'admissionDocuments',
            ]),
        ]);
    }

    public function status(Request $request, AdmissionApplication $admissionApplication): View
    {
        $this->authorizeApplicantApplication($request, $admissionApplication);

        return view('applicant.applications.status', [
            'application' => $admissionApplication->load([
                'admissionBatch',
                'program',
                'admissionDecision',
            ]),
        ]);
    }

    private function currentApplicant(Request $request): Applicant
    {
        return Applicant::query()
            ->where('user_id', $request->user()->id)
            ->orWhere(function ($query) use ($request): void {
                $query
                    ->whereNull('user_id')
                    ->where('email', $request->user()->email);
            })
            ->firstOrFail();
    }

    private function authorizeApplicantApplication(Request $request, AdmissionApplication $admissionApplication): void
    {
        abort_unless($admissionApplication->applicant_id === $this->currentApplicant($request)->id, 403);
    }

    private function newApplicationNumber(): string
    {
        do {
            $number = 'ADM-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (AdmissionApplication::where('application_no', $number)->exists());

        return $number;
    }
}
