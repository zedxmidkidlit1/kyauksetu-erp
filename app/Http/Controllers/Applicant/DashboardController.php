<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\AdmissionApplication;
use App\Models\AdmissionBatch;
use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $applicant = $this->currentApplicant($request);

        $applications = AdmissionApplication::query()
            ->whereBelongsTo($applicant)
            ->with(['admissionBatch', 'program', 'admissionDecision'])
            ->latest()
            ->get();

        return view('applicant.dashboard', [
            'applicant' => $applicant,
            'applications' => $applications,
            'latestApplication' => $applications->first(),
            'openBatchCount' => AdmissionBatch::query()->where('status', 'open')->count(),
        ]);
    }

    private function currentApplicant(Request $request): Applicant
    {
        return Applicant::where('email', $request->user()->email)->firstOrFail();
    }
}
