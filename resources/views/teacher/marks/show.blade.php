@extends('layouts.teacher', ['title' => 'Marks Entry'])

@section('content')
    <div class="page-header">
        <div>
            <h1>{{ $assessmentComponent->name }}</h1>
            <p class="muted">
                {{ $assessmentComponent->course?->name ?? '-' }} /
                {{ $assessmentComponent->classSection?->name ?? '-' }} /
                Max {{ $assessmentComponent->max_marks }}
            </p>
        </div>
        <a href="{{ route('teacher.marks.index') }}">Back to marks</a>
    </div>

    <x-teacher.card title="Component details">
        <p><strong>Academic year:</strong> {{ $assessmentComponent->academicYear?->name ?? '-' }}</p>
        <p><strong>Semester:</strong> {{ $assessmentComponent->semester?->name ?? '-' }}</p>
        <p><strong>Type:</strong> {{ ucwords(str_replace('_', ' ', $assessmentComponent->component_type)) }}</p>
        <p><strong>Status:</strong> <x-teacher.badge :value="$assessmentComponent->status" /></p>
    </x-teacher.card>

    <div style="margin-top: 1rem;">
        @if ($students->isEmpty())
            <x-teacher.empty-state message="No enrolled students were found for this component." />
        @else
            <form method="POST" action="{{ route('teacher.marks.components.students.update', $assessmentComponent) }}">
                @csrf

                <x-teacher.card>
                    <x-teacher.table>
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Roll no</th>
                                <th>Marks</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $enrollment)
                                @php($mark = $marksByEnrollment->get($enrollment->id))
                                <tr>
                                    <td>
                                        <strong>{{ $enrollment->studentProfile?->user?->name ?? $enrollment->studentProfile?->student_no ?? '-' }}</strong>
                                        <div class="muted">{{ $enrollment->studentProfile?->student_no ?? '-' }}</div>
                                    </td>
                                    <td>{{ $enrollment->roll_no ?? '-' }}</td>
                                    <td>
                                        <input
                                            name="records[{{ $enrollment->id }}][marks_obtained]"
                                            type="number"
                                            min="0"
                                            max="{{ $assessmentComponent->max_marks }}"
                                            step="0.01"
                                            value="{{ old("records.{$enrollment->id}.marks_obtained", $mark?->marks_obtained) }}"
                                        >
                                        @error("records.{$enrollment->id}.marks_obtained") <div class="error">{{ $message }}</div> @enderror
                                    </td>
                                    <td>
                                        <input name="records[{{ $enrollment->id }}][remarks]" type="text" value="{{ old("records.{$enrollment->id}.remarks", $mark?->remarks) }}">
                                        @error("records.{$enrollment->id}.remarks") <div class="error">{{ $message }}</div> @enderror
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-teacher.table>
                </x-teacher.card>

                <div style="margin-top: 1rem;">
                    <button class="button" type="submit">Save marks</button>
                </div>
            </form>
        @endif
    </div>
@endsection
