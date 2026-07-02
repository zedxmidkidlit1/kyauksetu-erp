@extends('layouts.student', ['title' => 'Hostel'])

@section('content')
    <div class="page-header">
        <div>
            <h1>Hostel</h1>
            <p class="muted">Hostel allocations linked to your profile.</p>
        </div>
    </div>

    <x-student.card>
        @if ($allocations->isEmpty())
            <x-student.empty-state message="No hostel allocation records found." />
        @else
            <x-student.table>
                <thead>
                    <tr>
                        <th>Hostel</th>
                        <th>Room</th>
                        <th>Bed</th>
                        <th>Allocated</th>
                        <th>Vacated</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($allocations as $allocation)
                        <tr>
                            <td>{{ $allocation->hostel?->name ?? '-' }}</td>
                            <td>{{ $allocation->hostelRoom?->name ?? $allocation->hostelRoom?->room_no ?? '-' }}</td>
                            <td>{{ $allocation->hostelBed?->bed_no ?? '-' }}</td>
                            <td>{{ $allocation->allocated_at?->toFormattedDateString() ?? '-' }}</td>
                            <td>{{ $allocation->vacated_at?->toFormattedDateString() ?? '-' }}</td>
                            <td><x-student.badge :value="$allocation->allocation_status" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </x-student.table>
        @endif
    </x-student.card>
@endsection
