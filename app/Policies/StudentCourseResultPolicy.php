<?php

namespace App\Policies;

use App\Models\StudentCourseResult;
use App\Models\User;

class StudentCourseResultPolicy
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
        return $user->can('student_course_results.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StudentCourseResult $studentCourseResult): bool
    {
        return $user->can('student_course_results.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('student_course_results.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StudentCourseResult $studentCourseResult): bool
    {
        return $user->can('student_course_results.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StudentCourseResult $studentCourseResult): bool
    {
        return $user->can('student_course_results.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StudentCourseResult $studentCourseResult): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StudentCourseResult $studentCourseResult): bool
    {
        return false;
    }
}
