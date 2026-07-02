@extends('layouts.applicant', ['title' => 'Dashboard'])

@section('content')
    <div class="card-header">
        <div>
            <h1>Welcome, {{ $applicant->first_name }}</h1>
            <p class="muted">{{ $applicant->applicant_no ?? 'Applicant profile' }}</p>
        </div>
        <a class="button" href="{{ route('applicant.applications.create') }}">New application</a>
    </div>

    <div class="grid grid-2">
        <div class="card">
            <h2>Applications</h2>
            <div class="stat">{{ $applications->count() }}</div>
            <p class="muted">Total applications submitted from this account.</p>
        </div>
        <div class="card">
            <h2>Open batches</h2>
            <div class="stat">{{ $openBatchCount }}</div>
            <p class="muted">Admission batches currently available.</p>
        </div>
    </div>

    <div class="card" style="margin-top: 1rem;">
        <div class="card-header">
            <div>
                <h2>Latest application</h2>
                <p class="muted">Your most recent admission activity.</p>
            </div>
            <a href="{{ route('applicant.applications.index') }}">View all</a>
        </div>

        @if ($latestApplication)
            <p><strong>{{ $latestApplication->application_no }}</strong></p>
            <p class="muted">
                {{ $latestApplication->admissionBatch?->name ?? 'Admission batch' }}
                @if ($latestApplication->program)
                    · {{ $latestApplication->program->name }}
                @endif
            </p>
            <p><span class="badge">{{ str_replace('_', ' ', $latestApplication->application_status) }}</span></p>
            <a class="button secondary" href="{{ route('applicant.applications.status', $latestApplication) }}">Check status</a>
        @else
            <p class="muted">No applications yet.</p>
        @endif
    </div>
@endsection
