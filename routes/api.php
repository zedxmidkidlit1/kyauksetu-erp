<?php

use App\Http\Controllers\Api\V1\KaiChatController;
use App\Http\Controllers\Api\V1\KaiContextController;
use App\Http\Controllers\Api\V1\StudentDataController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->middleware('auth:sanctum')
    ->group(function (): void {
        Route::post('kai/chat', KaiChatController::class)->name('kai.chat');
        Route::get('kai/context', KaiContextController::class)->name('kai.context');

        Route::controller(StudentDataController::class)->group(function (): void {
            Route::get('me', 'me')->name('me');
            Route::get('my-profile', 'myProfile')->name('my-profile');
            Route::get('my-enrollment', 'myEnrollment')->name('my-enrollment');
            Route::get('my-timetable', 'myTimetable')->name('my-timetable');
            Route::get('my-attendance', 'myAttendance')->name('my-attendance');
            Route::get('my-results', 'myResults')->name('my-results');
            Route::get('my-fees', 'myFees')->name('my-fees');
            Route::get('my-library', 'myLibrary')->name('my-library');
            Route::get('my-hostel', 'myHostel')->name('my-hostel');
            Route::get('announcements', 'announcements')->name('announcements');
        });
    });
