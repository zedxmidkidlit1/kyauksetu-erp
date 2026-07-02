@extends('layouts.teacher', ['title' => 'Classes'])

@section('content')
    <div class="page-header">
        <div>
            <h1>Classes</h1>
            <p class="muted">Students from your assigned class sections.</p>
        </div>
    </div>

    @if ($students->isEmpty())
        <x-teacher.empty-state message="No students are available for your assigned classes yet." />
    @else
        <x-teacher.card>
            <x-teacher.table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Roll no</th>
                        <th>Class</th>
                        <th>Program</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $enrollment)
                        <tr>
                            <td>
                                <strong>{{ $enrollment->studentProfile?->user?->name ?? $enrollment->studentProfile?->student_no ?? '-' }}</strong>
                                <div class="muted">{{ $enrollment->studentProfile?->student_no ?? '-' }}</div>
                            </td>
                            <td>{{ $enrollment->roll_no ?? '-' }}</td>
                            <td>{{ $enrollment->classSection?->name ?? '-' }}</td>
                            <td>{{ $enrollment->program?->name ?? '-' }}{{ $enrollment->major ? ' - '.$enrollment->major->name : '' }}</td>
                            <td><x-teacher.badge :value="$enrollment->status" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </x-teacher.table>
        </x-teacher.card>
    @endif
@endsection
