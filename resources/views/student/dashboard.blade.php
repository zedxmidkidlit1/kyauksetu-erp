@extends('layouts.student', ['title' => 'Dashboard'])

@section('content')
    <div class="page-header">
        <div>
            <h1>Welcome, {{ $profile->first_name ?? $profile->user->name }}</h1>
            <p class="muted">{{ $profile->student_no ?? 'Student profile' }}</p>
        </div>
    </div>

    <div class="grid grid-3">
        <x-student.card title="Current enrollment">
            <div class="stat">{{ $currentEnrollment?->roll_no ?? '-' }}</div>
            <p class="muted">{{ $currentEnrollment?->program?->name ?? 'No active enrollment' }}</p>
        </x-student.card>
        <x-student.card title="Results">
            <div class="stat">{{ $resultCount }}</div>
            <p class="muted">Course results linked to your enrollments.</p>
        </x-student.card>
        <x-student.card title="Fees">
            <div class="stat">{{ $feeCount }}</div>
            <p class="muted">Fee records linked to your profile.</p>
        </x-student.card>
    </div>

    <div class="grid grid-2" style="margin-top: 1rem;">
        <x-student.card title="Academic placement">
            <p><strong>Academic year:</strong> {{ $profile->academicYear?->name ?? '-' }}</p>
            <p><strong>Program:</strong> {{ $profile->program?->name ?? '-' }}</p>
            <p><strong>Major:</strong> {{ $profile->major?->name ?? '-' }}</p>
            <p><strong>Class section:</strong> {{ $profile->classSection?->name ?? '-' }}</p>
        </x-student.card>

        <x-student.card title="Announcements">
            <div class="stat">{{ $announcementCount }}</div>
            <p class="muted">Published announcements visible to your student profile.</p>
            <a href="{{ route('student.announcements') }}">View announcements</a>
        </x-student.card>
    </div>
@endsection
