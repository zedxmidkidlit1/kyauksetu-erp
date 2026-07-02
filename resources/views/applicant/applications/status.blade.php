@extends('layouts.applicant', ['title' => 'Application Status'])

@section('content')
    <div class="card">
        <h1>Application status</h1>
        <p class="muted">{{ $application->application_no }}</p>

        <div class="grid grid-2">
            <div>
                <h2>Review status</h2>
                <p><span class="badge">{{ str_replace('_', ' ', $application->application_status) }}</span></p>
                <p class="muted">{{ $application->admissionBatch?->name ?? 'Admission batch' }}</p>
            </div>

            <div>
                <h2>Decision</h2>
                @if ($application->admissionDecision)
                    <p><span class="badge">{{ str_replace('_', ' ', $application->admissionDecision->decision_status) }}</span></p>
                    <p class="muted">Updated {{ $application->admissionDecision->updated_at->toDayDateTimeString() }}</p>
                @else
                    <p class="muted">Decision pending.</p>
                @endif
            </div>
        </div>

        <a class="button secondary" href="{{ route('applicant.applications.show', $application) }}">View details</a>
        <a class="button secondary" href="{{ route('applicant.applications.index') }}">All applications</a>
    </div>
@endsection
