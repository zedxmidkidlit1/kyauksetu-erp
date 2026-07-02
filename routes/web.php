<?php

use App\Http\Controllers\Applicant\ApplicationController;
use App\Http\Controllers\Applicant\AuthController;
use App\Http\Controllers\Applicant\DashboardController;
use App\Http\Controllers\Student\AuthController as StudentAuthController;
use App\Http\Controllers\Student\PortalController as StudentPortalController;
use App\Http\Controllers\Teacher\AuthController as TeacherAuthController;
use App\Http\Controllers\Teacher\PortalController as TeacherPortalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('applicant')
    ->name('applicant.')
    ->group(function (): void {
        Route::get('register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('register', [AuthController::class, 'register'])->name('register.store');
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.store');

        Route::middleware('applicant')->group(function (): void {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/', DashboardController::class)->name('dashboard');
            Route::get('applications', [ApplicationController::class, 'index'])->name('applications.index');
            Route::get('applications/create', [ApplicationController::class, 'create'])->name('applications.create');
            Route::post('applications', [ApplicationController::class, 'store'])->name('applications.store');
            Route::get('applications/{admissionApplication}', [ApplicationController::class, 'show'])->name('applications.show');
            Route::get('applications/{admissionApplication}/status', [ApplicationController::class, 'status'])->name('applications.status');
        });
    });

Route::prefix('student')
    ->name('student.')
    ->group(function (): void {
        Route::get('login', [StudentAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [StudentAuthController::class, 'login'])->name('login.store');

        Route::middleware('student')->group(function (): void {
            Route::post('logout', [StudentAuthController::class, 'logout'])->name('logout');
            Route::get('/', [StudentPortalController::class, 'dashboard'])->name('dashboard');
            Route::get('profile', [StudentPortalController::class, 'profile'])->name('profile');
            Route::get('enrollment', [StudentPortalController::class, 'enrollment'])->name('enrollment');
            Route::get('timetable', [StudentPortalController::class, 'timetable'])->name('timetable');
            Route::get('attendance', [StudentPortalController::class, 'attendance'])->name('attendance');
            Route::get('results', [StudentPortalController::class, 'results'])->name('results');
            Route::get('fees', [StudentPortalController::class, 'fees'])->name('fees');
            Route::get('library', [StudentPortalController::class, 'library'])->name('library');
            Route::get('hostel', [StudentPortalController::class, 'hostel'])->name('hostel');
            Route::get('announcements', [StudentPortalController::class, 'announcements'])->name('announcements');
        });
    });

Route::prefix('teacher')
    ->name('teacher.')
    ->group(function (): void {
        Route::get('login', [TeacherAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [TeacherAuthController::class, 'login'])->name('login.store');

        Route::middleware('teacher')->group(function (): void {
            Route::post('logout', [TeacherAuthController::class, 'logout'])->name('logout');
            Route::get('/', [TeacherPortalController::class, 'dashboard'])->name('dashboard');
            Route::get('profile', [TeacherPortalController::class, 'profile'])->name('profile');
            Route::get('assignments', [TeacherPortalController::class, 'assignments'])->name('assignments');
            Route::get('timetable', [TeacherPortalController::class, 'timetable'])->name('timetable');
            Route::get('classes', [TeacherPortalController::class, 'classes'])->name('classes');
            Route::get('announcements', [TeacherPortalController::class, 'announcements'])->name('announcements');
        });
    });
