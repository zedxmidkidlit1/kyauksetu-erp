@extends('layouts.student', ['title' => 'Results'])

@section('content')
    <div class="page-header">
        <div>
            <h1>Results</h1>
            <p class="muted">Course results linked to your enrollments.</p>
        </div>
    </div>

    <x-student.card>
        @if ($results->isEmpty())
            <x-student.empty-state message="No result records found." />
        @else
            <x-student.table>
                <thead>
                    <tr>
                        <th>Academic year</th>
                        <th>Semester</th>
                        <th>Course</th>
                        <th>Total</th>
                        <th>Grade</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($results as $result)
                        <tr>
                            <td>{{ $result->academicYear?->name ?? '-' }}</td>
                            <td>{{ $result->semester?->name ?? '-' }}</td>
                            <td>{{ $result->course?->name ?? '-' }}</td>
                            <td>{{ $result->total_marks ?? '-' }}</td>
                            <td>{{ $result->grade ?? '-' }}</td>
                            <td><x-student.badge :value="$result->result_status" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </x-student.table>
        @endif
    </x-student.card>
@endsection
