@extends('layouts.student', ['title' => 'Enrollment'])

@section('content')
    <div class="page-header">
        <div>
            <h1>Enrollment</h1>
            <p class="muted">Your enrollment records.</p>
        </div>
    </div>

    <x-student.card>
        @if ($enrollments->isEmpty())
            <x-student.empty-state message="No enrollment records found." />
        @else
            <x-student.table>
                <thead>
                    <tr>
                        <th>Academic year</th>
                        <th>Semester</th>
                        <th>Program</th>
                        <th>Major</th>
                        <th>Class</th>
                        <th>Roll no</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($enrollments as $enrollment)
                        <tr>
                            <td>{{ $enrollment->academicYear?->name ?? '-' }}</td>
                            <td>{{ $enrollment->semester?->name ?? '-' }}</td>
                            <td>{{ $enrollment->program?->name ?? '-' }}</td>
                            <td>{{ $enrollment->major?->name ?? '-' }}</td>
                            <td>{{ $enrollment->classSection?->name ?? '-' }}</td>
                            <td>{{ $enrollment->roll_no ?? '-' }}</td>
                            <td><x-student.badge :value="$enrollment->status" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </x-student.table>
        @endif
    </x-student.card>
@endsection
