<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeacher
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('teacher.login');
        }

        if (! $request->user()?->hasRole('teacher')) {
            abort(403);
        }

        if (! $request->user()->teacherProfile()->exists()) {
            abort(403);
        }

        return $next($request);
    }
}
