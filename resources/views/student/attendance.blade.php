@extends('layouts.student', ['title' => 'Attendance'])

@section('content')
    <div class="page-header">
        <div>
            <h1>Attendance</h1>
            <p class="muted">Attendance records linked to your enrollments.</p>
        </div>
    </div>

    <x-student.card>
        @if ($records->isEmpty())
            <x-student.empty-state message="No attendance records found." />
        @else
            <x-student.table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Course</th>
                        <th>Time</th>
                        <th>Room</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <td>{{ $record->attendanceSession?->session_date?->toFormattedDateString() ?? '-' }}</td>
                            <td>{{ $record->attendanceSession?->course?->name ?? '-' }}</td>
                            <td>{{ $record->attendanceSession?->starts_at ?? '-' }} - {{ $record->attendanceSession?->ends_at ?? '-' }}</td>
                            <td>{{ $record->attendanceSession?->room?->name ?? '-' }}</td>
                            <td><x-student.badge :value="$record->status" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </x-student.table>
        @endif
    </x-student.card>
@endsection
