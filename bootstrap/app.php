<?php

use App\Http\Middleware\EnsureApplicant;
use App\Http\Middleware\EnsureMobileRole;
use App\Http\Middleware\EnsureStudent;
use App\Http\Middleware\EnsureTeacher;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'ability' => CheckForAnyAbility::class,
            'applicant' => EnsureApplicant::class,
            'mobile.role' => EnsureMobileRole::class,
            'student' => EnsureStudent::class,
            'teacher' => EnsureTeacher::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
