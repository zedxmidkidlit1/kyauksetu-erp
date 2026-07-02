@extends('layouts.student', ['title' => 'Announcements'])

@section('content')
    <div class="page-header">
        <div>
            <h1>Announcements</h1>
            <p class="muted">Published announcements visible to your student profile.</p>
        </div>
    </div>

    <div class="stack">
        @forelse ($announcements as $announcement)
            <x-student.card :title="$announcement->title" :subtitle="$announcement->publish_at?->toDayDateTimeString()">
                <p>{{ $announcement->body }}</p>
                <p>
                    <x-student.badge :value="$announcement->announcement_type" />
                    <x-student.badge :value="$announcement->priority" />
                </p>
            </x-student.card>
        @empty
            <x-student.card>
                <x-student.empty-state message="No announcements found." />
            </x-student.card>
        @endforelse
    </div>
@endsection
