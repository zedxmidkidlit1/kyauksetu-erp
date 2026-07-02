@extends('layouts.applicant', ['title' => 'Applications'])

@section('content')
    <div class="card-header">
        <div>
            <h1>Applications</h1>
            <p class="muted">Review applications submitted from your account.</p>
        </div>
        <a class="button" href="{{ route('applicant.applications.create') }}">New application</a>
    </div>

    <div class="card table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Application</th>
                    <th>Batch</th>
                    <th>Program</th>
                    <th>Status</th>
                    <th>Decision</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($applications as $application)
                    <tr>
                        <td>{{ $application->application_no }}</td>
                        <td>{{ $application->admissionBatch?->name ?? '-' }}</td>
                        <td>{{ $application->program?->name ?? '-' }}</td>
                        <td><span class="badge">{{ str_replace('_', ' ', $application->application_status) }}</span></td>
                        <td>{{ $application->admissionDecision?->decision_status ? str_replace('_', ' ', $application->admissionDecision->decision_status) : '-' }}</td>
                        <td>
                            <a href="{{ route('applicant.applications.show', $application) }}">View</a>
                            ·
                            <a href="{{ route('applicant.applications.status', $application) }}">Status</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No applications yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
