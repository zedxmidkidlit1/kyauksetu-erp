<?php

namespace App\Services\Kai;

use App\Models\User;

class KaiContextResolver
{
    public function __construct(
        private readonly StudentContextBuilder $studentContextBuilder,
        private readonly TeacherContextBuilder $teacherContextBuilder,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function buildFor(User $user): array
    {
        if ($user->hasRole('student')) {
            return $this->studentContextBuilder->buildFor($user);
        }

        if ($user->hasRole('teacher')) {
            return $this->teacherContextBuilder->buildFor($user);
        }

        abort(403, 'KAI context is only available for supported student or teacher accounts.');
    }
}
