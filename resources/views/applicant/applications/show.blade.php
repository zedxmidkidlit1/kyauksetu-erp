@extends('layouts.applicant', ['title' => 'Application Details'])

@section('content')
    <div class="card-header">
        <div>
            <h1>{{ $application->application_no }}</h1>
            <p class="muted">{{ $application->admissionBatch?->name ?? 'Admission batch' }}</p>
        </div>
        <a class="button secondary" href="{{ route('applicant.applications.status', $application) }}">Status</a>
    </div>

    <div class="grid grid-2">
        <div class="card">
            <h2>Application</h2>
            <p><strong>Status:</strong> <span class="badge">{{ str_replace('_', ' ', $application->application_status) }}</span></p>
            <p><strong>Applied:</strong> {{ $application->applied_at?->toDayDateTimeString() ?? '-' }}</p>
            <p><strong>Academic year:</strong> {{ $application->academicYear?->name ?? '-' }}</p>
            <p><strong>Program:</strong> {{ $application->program?->name ?? '-' }}</p>
            <p><strong>Major:</strong> {{ $application->major?->name ?? '-' }}</p>
        </div>

        <div class="card">
            <h2>Decision</h2>
            @if ($application->admissionDecision)
                <p><strong>Status:</strong> <span class="badge">{{ str_replace('_', ' ', $application->admissionDecision->decision_status) }}</span></p>
                <p><strong>Decided:</strong> {{ $application->admissionDecision->decided_at?->toDayDateTimeString() ?? '-' }}</p>
                <p><strong>Offer expires:</strong> {{ $application->admissionDecision->offer_expires_at?->toDayDateTimeString() ?? '-' }}</p>
            @else
                <p class="muted">No decision has been recorded yet.</p>
            @endif
        </div>
    </div>

    <div class="card" style="margin-top: 1rem;">
        <h2>Documents</h2>
        @if ($application->admissionDocuments->isEmpty())
            <p class="muted">No document metadata has been recorded yet.</p>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Document</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Verified</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($application->admissionDocuments as $document)
                            <tr>
                                <td>{{ $document->title }}</td>
                                <td>{{ str_replace('_', ' ', $document->document_type) }}</td>
                                <td><span class="badge">{{ str_replace('_', ' ', $document->document_status) }}</span></td>
                                <td>{{ $document->verified_at?->toDayDateTimeString() ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
