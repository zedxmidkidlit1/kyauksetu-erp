<?php

namespace App\Policies;

use App\Models\GradeScale;
use App\Models\User;

class GradeScalePolicy
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
        return $user->can('grade_scales.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, GradeScale $gradeScale): bool
    {
        return $user->can('grade_scales.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('grade_scales.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, GradeScale $gradeScale): bool
    {
        return $user->can('grade_scales.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GradeScale $gradeScale): bool
    {
        return $user->can('grade_scales.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, GradeScale $gradeScale): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, GradeScale $gradeScale): bool
    {
        return false;
    }
}
