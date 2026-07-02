@extends('layouts.teacher', ['title' => 'Announcements'])

@section('content')
    <div class="page-header">
        <div>
            <h1>Announcements</h1>
            <p class="muted">Published announcements visible to your teacher profile.</p>
        </div>
    </div>

    @if ($announcements->isEmpty())
        <x-teacher.empty-state message="No announcements are available right now." />
    @else
        <div class="stack">
            @foreach ($announcements as $announcement)
                <x-teacher.card title="{{ $announcement->title }}" subtitle="{{ $announcement->publish_at?->toFormattedDateString() ?? 'Published' }}">
                    <p>{{ $announcement->body }}</p>
                    <p><x-teacher.badge :value="$announcement->priority" /> <x-teacher.badge :value="$announcement->status" /></p>
                </x-teacher.card>
            @endforeach
        </div>
    @endif
@endsection
