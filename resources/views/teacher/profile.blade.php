@extends('layouts.teacher', ['title' => 'Profile'])

@section('content')
    <div class="page-header">
        <div>
            <h1>Profile</h1>
            <p class="muted">Your teacher profile and contact summary.</p>
        </div>
    </div>

    <x-teacher.card title="{{ $profile->user->name }}" subtitle="{{ $profile->staff_no ?? 'Teacher profile' }}">
        <p><strong>Email:</strong> {{ $profile->institutional_email ?? $profile->user->email }}</p>
        <p><strong>Department:</strong> {{ $profile->department?->name ?? '-' }}</p>
        <p><strong>Position:</strong> {{ $profile->position ?? '-' }}</p>
        <p><strong>Rank:</strong> {{ $profile->rank ?? '-' }}</p>
        <p><strong>Status:</strong> <x-teacher.badge :value="$profile->status" /></p>
    </x-teacher.card>
@endsection
