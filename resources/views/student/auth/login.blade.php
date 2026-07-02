@extends('layouts.student', ['title' => 'Login'])

@section('content')
    <x-student.card title="Student login" subtitle="Sign in to view your student records.">
        <form method="POST" action="{{ route('student.login.store') }}">
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
        </form>
    </x-student.card>
@endsection
