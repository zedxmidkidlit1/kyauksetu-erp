@extends('layouts.student', ['title' => 'Profile'])

@section('content')
    <div class="page-header">
        <div>
            <h1>Profile</h1>
            <p class="muted">Read-only student profile details.</p>
        </div>
    </div>

    <div class="grid grid-2">
        <x-student.card title="Identity">
            <p><strong>Student no:</strong> {{ $profile->student_no ?? '-' }}</p>
            <p><strong>Name:</strong> {{ trim(($profile->first_name ?? '').' '.($profile->last_name ?? '')) ?: $profile->user->name }}</p>
            <p><strong>Email:</strong> {{ $profile->institutional_email ?? $profile->user->email }}</p>
            <p><strong>Phone:</strong> {{ $profile->phone ?? '-' }}</p>
            <p><strong>Date of birth:</strong> {{ $profile->date_of_birth?->toFormattedDateString() ?? '-' }}</p>
        </x-student.card>

        <x-student.card title="Academic">
            <p><strong>Status:</strong> <x-student.badge :value="$profile->status" /></p>
            <p><strong>Department:</strong> {{ $profile->department?->name ?? '-' }}</p>
            <p><strong>Program:</strong> {{ $profile->program?->name ?? '-' }}</p>
            <p><strong>Major:</strong> {{ $profile->major?->name ?? '-' }}</p>
            <p><strong>Admission year:</strong> {{ $profile->admission_year ?? '-' }}</p>
        </x-student.card>
    </div>
@endsection
