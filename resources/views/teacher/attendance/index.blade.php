@extends('layouts.teacher', ['title' => 'Attendance'])

@section('content')
    <div class="page-header">
        <div>
            <h1>Attendance</h1>
            <p class="muted">Create attendance sessions for your assigned classes.</p>
        </div>
    </div>

    <div class="grid grid-2">
        <x-teacher.card title="New session" subtitle="Select one of your teaching assignments.">
            @if ($assignments->isEmpty())
                <x-teacher.empty-state message="No teaching assignments are available for attendance." />
            @else
                <form method="POST" action="{{ route('teacher.attendance.sessions.store') }}">
                    @csrf

                    <div class="field">
                        <label for="teaching_assignment_id">Assignment</label>
                        <select id="teaching_assignment_id" name="teaching_assignment_id" required>
                            @foreach ($assignments as $assignment)
                                <option value="{{ $assignment->id }}" @selected((int) old('teaching_assignment_id') === $assignment->id)>
                                    {{ $assignment->course?->name ?? 'Course' }} / {{ $assignment->classSection?->name ?? 'Class' }} / {{ $assignment->academicYear?->name ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('teaching_assignment_id') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label for="session_date">Date</label>
                        <input id="session_date" name="session_date" type="date" value="{{ old('session_date', now()->toDateString()) }}" required>
                        @error('session_date') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="grid grid-2">
                        <div class="field">
                            <label for="starts_at">Start time</label>
                            <input id="starts_at" name="starts_at" type="time" value="{{ old('starts_at') }}">
                            @error('starts_at') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field">
                            <label for="ends_at">End time</label>
                            <input id="ends_at" name="ends_at" type="time" value="{{ old('ends_at') }}">
                            @error('ends_at') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="field">
                        <label for="remarks">Remarks</label>
                        <input id="remarks" name="remarks" type="text" value="{{ old('remarks') }}">
                        @error('remarks') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <button class="button" type="submit">Create session</button>
                </form>
            @endif
        </x-teacher.card>

        <x-teacher.card title="Recent sessions" subtitle="Sessions tied to your assignments.">
            @if ($sessions->isEmpty())
                <x-teacher.empty-state message="No attendance sessions are available yet." />
            @else
                <div class="stack">
                    @foreach ($sessions as $session)
                        <div>
                            <h3><a href="{{ route('teacher.attendance.sessions.show', $session) }}">{{ $session->course?->name ?? $session->teachingAssignment?->course?->name ?? 'Attendance session' }}</a></h3>
                            <p class="muted">
                                {{ $session->classSection?->name ?? '-' }} /
                                {{ $session->session_date?->toFormattedDateString() ?? '-' }} /
                                <x-teacher.badge :value="$session->status" />
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-teacher.card>
    </div>
@endsection
