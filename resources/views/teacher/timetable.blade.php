@extends('layouts.teacher', ['title' => 'Timetable'])

@section('content')
    <div class="page-header">
        <div>
            <h1>Timetable</h1>
            <p class="muted">Timetable slots assigned to you.</p>
        </div>
    </div>

    @if ($timetables->isEmpty())
        <x-teacher.empty-state message="No timetable slots are available yet." />
    @else
        <div class="stack">
            @foreach ($timetables as $timetable)
                <x-teacher.card title="{{ $timetable->name }}" subtitle="{{ $timetable->classSection?->name ?? 'Class section' }}">
                    @if ($timetable->slots->isEmpty())
                        <x-teacher.empty-state message="No slots are available for this timetable." />
                    @else
                        <x-teacher.table>
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Time</th>
                                    <th>Course</th>
                                    <th>Room</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($timetable->slots as $slot)
                                    <tr>
                                        <td>{{ ucfirst((string) $slot->day_of_week) }}</td>
                                        <td>{{ $slot->starts_at ? str($slot->starts_at)->substr(0, 5) : '-' }} - {{ $slot->ends_at ? str($slot->ends_at)->substr(0, 5) : '-' }}</td>
                                        <td>{{ $slot->course?->name ?? $slot->teachingAssignment?->course?->name ?? '-' }}</td>
                                        <td>{{ $slot->room?->name ?? '-' }}</td>
                                        <td><x-teacher.badge :value="$slot->status" /></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-teacher.table>
                    @endif
                </x-teacher.card>
            @endforeach
        </div>
    @endif
@endsection
