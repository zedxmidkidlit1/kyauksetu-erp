@extends('layouts.teacher', ['title' => 'Assignments'])

@section('content')
    <div class="page-header">
        <div>
            <h1>Assignments</h1>
            <p class="muted">Read-only teaching assignments linked to your profile.</p>
        </div>
    </div>

    @if ($assignments->isEmpty())
        <x-teacher.empty-state message="No teaching assignments are available yet." />
    @else
        <x-teacher.card>
            <x-teacher.table>
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Class</th>
                        <th>Academic term</th>
                        <th>Program</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assignments as $assignment)
                        <tr>
                            <td>
                                <strong>{{ $assignment->course?->name ?? '-' }}</strong>
                                <div class="muted">{{ $assignment->course?->code ?? '-' }}</div>
                            </td>
                            <td>{{ $assignment->classSection?->name ?? '-' }}</td>
                            <td>{{ $assignment->academicYear?->name ?? '-' }} / {{ $assignment->semester?->name ?? '-' }}</td>
                            <td>{{ $assignment->program?->name ?? '-' }}{{ $assignment->major ? ' - '.$assignment->major->name : '' }}</td>
                            <td><x-teacher.badge :value="$assignment->status" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </x-teacher.table>
        </x-teacher.card>
    @endif
@endsection
