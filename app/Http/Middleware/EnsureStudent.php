<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudent
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('student.login');
        }

        if (! $request->user()?->hasRole('student')) {
            abort(403);
        }

        if (! $request->user()->studentProfile()->exists()) {
            abort(403);
        }

        return $next($request);
    }
}
