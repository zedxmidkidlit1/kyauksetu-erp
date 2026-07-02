@extends('layouts.teacher', ['title' => 'Marks'])

@section('content')
    <div class="page-header">
        <div>
            <h1>Marks</h1>
            <p class="muted">Enter marks for assessment components connected to your assigned classes.</p>
        </div>
    </div>

    @if ($components->isEmpty())
        <x-teacher.empty-state message="No assessment components are available for your assignments yet." />
    @else
        <x-teacher.card>
            <x-teacher.table>
                <thead>
                    <tr>
                        <th>Component</th>
                        <th>Course</th>
                        <th>Class</th>
                        <th>Academic term</th>
                        <th>Max</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($components as $assessmentComponent)
                        <tr>
                            <td>
                                <strong><a href="{{ route('teacher.marks.components.show', $assessmentComponent) }}">{{ $assessmentComponent->name }}</a></strong>
                                <div class="muted">{{ ucwords(str_replace('_', ' ', $assessmentComponent->component_type)) }} / {{ $assessmentComponent->student_marks_count }} marks</div>
                            </td>
                            <td>{{ $assessmentComponent->course?->name ?? '-' }}</td>
                            <td>{{ $assessmentComponent->classSection?->name ?? '-' }}</td>
                            <td>{{ $assessmentComponent->academicYear?->name ?? '-' }} / {{ $assessmentComponent->semester?->name ?? '-' }}</td>
                            <td>{{ $assessmentComponent->max_marks }}</td>
                            <td><x-teacher.badge :value="$assessmentComponent->status" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </x-teacher.table>
        </x-teacher.card>
    @endif
@endsection
