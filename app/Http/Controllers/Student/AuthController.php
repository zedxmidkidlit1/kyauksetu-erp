<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\LoginStudentRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('student.auth.login');
    }

    public function login(LoginStudentRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'The provided credentials do not match our records.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = $request->user();

        if (! $user instanceof User || ! $user->hasRole('student') || ! $user->studentProfile()->exists()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'This account is not linked to a student profile.'])
                ->onlyInput('email');
        }

        return redirect()->intended(route('student.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login');
    }
}
