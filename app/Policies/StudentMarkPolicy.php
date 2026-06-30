<?php

namespace App\Policies;

use App\Models\StudentMark;
use App\Models\User;

class StudentMarkPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('student_marks.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StudentMark $studentMark): bool
    {
        return $user->can('student_marks.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('student_marks.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StudentMark $studentMark): bool
    {
        return $user->can('student_marks.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StudentMark $studentMark): bool
    {
        return $user->can('student_marks.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StudentMark $studentMark): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StudentMark $studentMark): bool
    {
        return false;
    }
}
