@extends('layouts.teacher', ['title' => 'Dashboard'])

@section('content')
    <div class="page-header">
        <div>
            <h1>Welcome, {{ $profile->user->name }}</h1>
            <p class="muted">{{ $profile->staff_no ?? 'Teacher profile' }}</p>
        </div>
    </div>

    <div class="grid grid-3">
        <x-teacher.card title="Assignments">
            <div class="stat">{{ $assignmentCount }}</div>
            <p class="muted">Teaching assignments linked to your teacher profile.</p>
        </x-teacher.card>
        <x-teacher.card title="Classes">
            <div class="stat">{{ $classCount }}</div>
            <p class="muted">Class sections assigned to you.</p>
        </x-teacher.card>
        <x-teacher.card title="Students">
            <div class="stat">{{ $studentCount }}</div>
            <p class="muted">Students in your assigned classes.</p>
        </x-teacher.card>
    </div>

    <div class="grid grid-2" style="margin-top: 1rem;">
        <x-teacher.card title="Profile">
            <p><strong>Department:</strong> {{ $profile->department?->name ?? '-' }}</p>
            <p><strong>Position:</strong> {{ $profile->position ?? '-' }}</p>
            <p><strong>Rank:</strong> {{ $profile->rank ?? '-' }}</p>
            <p><strong>Status:</strong> <x-teacher.badge :value="$profile->status" /></p>
        </x-teacher.card>

        <x-teacher.card title="Announcements">
            <div class="stat">{{ $announcementCount }}</div>
            <p class="muted">Published announcements visible to your teacher profile.</p>
            <a href="{{ route('teacher.announcements') }}">View announcements</a>
        </x-teacher.card>
    </div>
@endsection
