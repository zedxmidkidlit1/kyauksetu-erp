@extends('layouts.student', ['title' => 'Library'])

@section('content')
    <div class="page-header">
        <div>
            <h1>Library</h1>
            <p class="muted">Library loans linked to your profile.</p>
        </div>
    </div>

    <x-student.card>
        @if ($loans->isEmpty())
            <x-student.empty-state message="No library loan records found." />
        @else
            <x-student.table>
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Copy</th>
                        <th>Borrowed</th>
                        <th>Due</th>
                        <th>Returned</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($loans as $loan)
                        <tr>
                            <td>{{ $loan->bookCopy?->book?->title ?? '-' }}</td>
                            <td>{{ $loan->bookCopy?->accession_no ?? '-' }}</td>
                            <td>{{ $loan->borrowed_at?->toFormattedDateString() ?? '-' }}</td>
                            <td>{{ $loan->due_at?->toFormattedDateString() ?? '-' }}</td>
                            <td>{{ $loan->returned_at?->toFormattedDateString() ?? '-' }}</td>
                            <td><x-student.badge :value="$loan->loan_status" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </x-student.table>
        @endif
    </x-student.card>
@endsection
