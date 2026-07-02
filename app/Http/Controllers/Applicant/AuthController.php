<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\LoginApplicantRequest;
use App\Http\Requests\Applicant\RegisterApplicantRequest;
use App\Models\Applicant;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('applicant.auth.register');
    }

    public function register(RegisterApplicantRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => trim("{$data['first_name']} {$data['last_name']}"),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Role::firstOrCreate([
            'name' => 'applicant',
            'guard_name' => 'web',
        ]);

        $user->assignRole('applicant');

        $applicant = Applicant::create([
            'user_id' => $user->id,
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'status' => 'active',
        ]);

        $applicant->update([
            'applicant_no' => 'APP-'.now()->format('Y').'-'.str_pad((string) $applicant->id, 6, '0', STR_PAD_LEFT),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('applicant.dashboard');
    }

    public function showLogin(): View
    {
        return view('applicant.auth.login');
    }

    public function login(LoginApplicantRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'The provided credentials do not match our records.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        if (! $request->user()?->hasRole('applicant')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'This account is not registered for the applicant portal.'])
                ->onlyInput('email');
        }

        if (! $this->applicantExistsFor($request->user())) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'Applicant profile was not found for this account.'])
                ->onlyInput('email');
        }

        return redirect()->intended(route('applicant.dashboard'));
    }

    private function applicantExistsFor(User $user): bool
    {
        return Applicant::query()
            ->where('user_id', $user->id)
            ->orWhere(function ($query) use ($user): void {
                $query
                    ->whereNull('user_id')
                    ->where('email', $user->email);
            })
            ->exists();
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('applicant.login');
    }
}
