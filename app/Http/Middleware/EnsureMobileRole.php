<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMobileRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless($user instanceof User, 401);

        $roles = $roles === [] ? ['student', 'teacher'] : $roles;

        abort_unless(
            $user->hasAnyRole($roles),
            403,
            'This mobile endpoint is not available for this account.',
        );

        if (in_array('student', $roles, true) && $user->hasRole('student')) {
            abort_unless(
                $user->studentProfile()->exists(),
                403,
                'This account is not linked to a student profile.',
            );
        }

        if (in_array('teacher', $roles, true) && $user->hasRole('teacher')) {
            abort_unless(
                $user->teacherProfile()->exists(),
                403,
                'This account is not linked to a teacher profile.',
            );
        }

        return $next($request);
    }
}
