@extends('layouts.teacher', ['title' => 'Attendance Session'])

@section('content')
    <div class="page-header">
        <div>
            <h1>{{ $session->course?->name ?? $session->teachingAssignment?->course?->name ?? 'Attendance session' }}</h1>
            <p class="muted">
                {{ $session->classSection?->name ?? '-' }} /
                {{ $session->session_date?->toFormattedDateString() ?? '-' }}
            </p>
        </div>
        <a href="{{ route('teacher.attendance.index') }}">Back to attendance</a>
    </div>

    <x-teacher.card title="Session details">
        <p><strong>Academic year:</strong> {{ $session->academicYear?->name ?? '-' }}</p>
        <p><strong>Semester:</strong> {{ $session->semester?->name ?? '-' }}</p>
        <p><strong>Time:</strong> {{ $session->starts_at ? str($session->starts_at)->substr(0, 5) : '-' }} - {{ $session->ends_at ? str($session->ends_at)->substr(0, 5) : '-' }}</p>
        <p><strong>Status:</strong> <x-teacher.badge :value="$session->status" /></p>
    </x-teacher.card>

    <div style="margin-top: 1rem;">
        @if ($session->records->isEmpty())
            <x-teacher.empty-state message="No students were found for this attendance session." />
        @else
            <form method="POST" action="{{ route('teacher.attendance.sessions.records.update', $session) }}">
                @csrf

                <x-teacher.card>
                    <x-teacher.table>
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Roll no</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($session->records as $record)
                                <tr>
                                    <td>
                                        <strong>{{ $record->studentEnrollment?->studentProfile?->user?->name ?? $record->studentEnrollment?->studentProfile?->student_no ?? '-' }}</strong>
                                        <div class="muted">{{ $record->studentEnrollment?->studentProfile?->student_no ?? '-' }}</div>
                                    </td>
                                    <td>{{ $record->studentEnrollment?->roll_no ?? '-' }}</td>
                                    <td>
                                        <select name="records[{{ $record->id }}][status]" required>
                                            @foreach ($statuses as $status)
                                                <option value="{{ $status }}" @selected(old("records.{$record->id}.status", $record->status) === $status)>
                                                    {{ ucwords($status) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("records.{$record->id}.status") <div class="error">{{ $message }}</div> @enderror
                                    </td>
                                    <td>
                                        <input name="records[{{ $record->id }}][remarks]" type="text" value="{{ old("records.{$record->id}.remarks", $record->remarks) }}">
                                        @error("records.{$record->id}.remarks") <div class="error">{{ $message }}</div> @enderror
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-teacher.table>
                </x-teacher.card>

                <div style="margin-top: 1rem;">
                    <button class="button" type="submit">Save attendance</button>
                </div>
            </form>
        @endif
    </div>
@endsection
