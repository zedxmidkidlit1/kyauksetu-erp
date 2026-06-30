<?php

namespace App\Policies;

use App\Models\ExamTerm;
use App\Models\User;

class ExamTermPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('exam_terms.view');
    }

    public function view(User $user, ExamTerm $examTerm): bool
    {
        return $user->can('exam_terms.view');
    }

    public function create(User $user): bool
    {
        return $user->can('exam_terms.create');
    }

    public function update(User $user, ExamTerm $examTerm): bool
    {
        return $user->can('exam_terms.update');
    }

    public function delete(User $user, ExamTerm $examTerm): bool
    {
        return $user->can('exam_terms.delete');
    }

    public function restore(User $user, ExamTerm $examTerm): bool
    {
        return false;
    }

    public function forceDelete(User $user, ExamTerm $examTerm): bool
    {
        return false;
    }
}
