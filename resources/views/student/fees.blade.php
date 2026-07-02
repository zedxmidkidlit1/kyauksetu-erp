@extends('layouts.student', ['title' => 'Fees'])

@section('content')
    <div class="page-header">
        <div>
            <h1>Fees</h1>
            <p class="muted">Read-only fee records and payments.</p>
        </div>
    </div>

    <x-student.card>
        @if ($fees->isEmpty())
            <x-student.empty-state message="No fee records found." />
        @else
            <x-student.table>
                <thead>
                    <tr>
                        <th>Fee</th>
                        <th>Academic year</th>
                        <th>Amount</th>
                        <th>Payable</th>
                        <th>Paid</th>
                        <th>Due</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($fees as $fee)
                        <tr>
                            <td>{{ $fee->feeType?->name ?? 'Fee' }}</td>
                            <td>{{ $fee->academicYear?->name ?? '-' }}</td>
                            <td>{{ $fee->amount }}</td>
                            <td>{{ $fee->payable_amount }}</td>
                            <td>{{ $fee->studentPayments->sum('amount') }}</td>
                            <td>{{ $fee->due_at?->toFormattedDateString() ?? '-' }}</td>
                            <td><x-student.badge :value="$fee->fee_status" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </x-student.table>
        @endif
    </x-student.card>
@endsection
