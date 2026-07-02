@extends('layouts.applicant', ['title' => 'New Application'])

@section('content')
    <div class="card">
        <h1>New application</h1>
        <p class="muted">Choose an open admission batch and submit your application.</p>

        @if ($batches->isEmpty())
            <p>No admission batches are open right now.</p>
            <a class="button secondary" href="{{ route('applicant.dashboard') }}">Back to dashboard</a>
        @else
            <form method="POST" action="{{ route('applicant.applications.store') }}">
                @csrf

                <div class="field">
                    <label for="admission_batch_id">Admission batch</label>
                    <select id="admission_batch_id" name="admission_batch_id" required>
                        <option value="">Select a batch</option>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch->id }}" @selected(old('admission_batch_id') == $batch->id)>
                                {{ $batch->name }}
                                @if ($batch->program)
                                    - {{ $batch->program->name }}
                                @endif
                                @if ($batch->closes_at)
                                    (closes {{ $batch->closes_at->toFormattedDateString() }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('admission_batch_id') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="grid grid-2">
                    <div class="field">
                        <label for="program_id">Preferred program</label>
                        <select id="program_id" name="program_id">
                            <option value="">Use batch program / undecided</option>
                            @foreach ($programs as $program)
                                <option value="{{ $program->id }}" @selected(old('program_id') == $program->id)>{{ $program->name }}</option>
                            @endforeach
                        </select>
                        @error('program_id') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label for="major_id">Preferred major</label>
                        <select id="major_id" name="major_id">
                            <option value="">Undecided</option>
                            @foreach ($majors as $major)
                                <option value="{{ $major->id }}" @selected(old('major_id') == $major->id)>{{ $major->name }}</option>
                            @endforeach
                        </select>
                        @error('major_id') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="field">
                    <label for="remarks">Remarks</label>
                    <textarea id="remarks" name="remarks">{{ old('remarks') }}</textarea>
                    @error('remarks') <div class="error">{{ $message }}</div> @enderror
                </div>

                <button class="button" type="submit">Submit application</button>
                <a class="button secondary" href="{{ route('applicant.applications.index') }}">Cancel</a>
            </form>
        @endif
    </div>
@endsection
