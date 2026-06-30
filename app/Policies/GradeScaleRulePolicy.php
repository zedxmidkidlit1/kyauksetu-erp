<?php

namespace App\Policies;

use App\Models\GradeScaleRule;
use App\Models\User;

class GradeScaleRulePolicy
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
        return $user->can('grade_scale_rules.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, GradeScaleRule $gradeScaleRule): bool
    {
        return $user->can('grade_scale_rules.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('grade_scale_rules.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, GradeScaleRule $gradeScaleRule): bool
    {
        return $user->can('grade_scale_rules.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GradeScaleRule $gradeScaleRule): bool
    {
        return $user->can('grade_scale_rules.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, GradeScaleRule $gradeScaleRule): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, GradeScaleRule $gradeScaleRule): bool
    {
        return false;
    }
}
