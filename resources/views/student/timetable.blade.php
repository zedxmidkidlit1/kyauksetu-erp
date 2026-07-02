@extends('layouts.student', ['title' => 'Timetable'])

@section('content')
    <div class="page-header">
        <div>
            <h1>Timetable</h1>
            <p class="muted">Schedules matching your current enrollment.</p>
        </div>
    </div>

    <div class="stack">
        @forelse ($timetables as $timetable)
            <x-student.card :title="$timetable->name" :subtitle="$timetable->classSection?->name ?? $timetable->program?->name">
                @if ($timetable->slots->isEmpty())
                    <x-student.empty-state message="No timetable slots found." />
                @else
                    <x-student.table>
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Time</th>
                                <th>Course</th>
                                <th>Teacher</th>
                                <th>Room</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($timetable->slots as $slot)
                                <tr>
                                    <td>{{ ucwords((string) $slot->day_of_week) }}</td>
                                    <td>{{ $slot->starts_at }} - {{ $slot->ends_at }}</td>
                                    <td>{{ $slot->course?->name ?? '-' }}</td>
                                    <td>{{ $slot->teacherProfile?->user?->name ?? $slot->teacherProfile?->staff_no ?? '-' }}</td>
                                    <td>{{ $slot->room?->name ?? '-' }}</td>
                                    <td><x-student.badge :value="$slot->status" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-student.table>
                @endif
            </x-student.card>
        @empty
            <x-student.card>
                <x-student.empty-state message="No timetable records found for your current enrollment." />
            </x-student.card>
        @endforelse
    </div>
@endsection
