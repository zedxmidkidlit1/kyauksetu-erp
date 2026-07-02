@extends('layouts.applicant', ['title' => 'Login'])

@section('content')
    <div class="card">
        <h1>Applicant login</h1>
        <p class="muted">Sign in to continue your admission application.</p>

        <form method="POST" action="{{ route('applicant.login.store') }}">
            @csrf

            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <button class="button" type="submit">Login</button>
            <a class="button secondary" href="{{ route('applicant.register') }}">Create account</a>
        </form>
    </div>
@endsection
