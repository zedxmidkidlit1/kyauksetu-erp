@extends('layouts.applicant', ['title' => 'Register'])

@section('content')
    <div class="card">
        <h1>Create applicant account</h1>
        <p class="muted">Register to submit and track your admission application.</p>

        <form method="POST" action="{{ route('applicant.register.store') }}">
            @csrf

            <div class="grid grid-2">
                <div class="field">
                    <label for="first_name">First name</label>
                    <input id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                    @error('first_name') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="middle_name">Middle name</label>
                    <input id="middle_name" name="middle_name" value="{{ old('middle_name') }}">
                    @error('middle_name') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="last_name">Last name</label>
                    <input id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                    @error('last_name') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" value="{{ old('phone') }}">
                    @error('phone') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                    @error('email') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div></div>

                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" required>
                    @error('password') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required>
                </div>
            </div>

            <button class="button" type="submit">Create account</button>
            <a class="button secondary" href="{{ route('applicant.login') }}">I already have an account</a>
        </form>
    </div>
@endsection
