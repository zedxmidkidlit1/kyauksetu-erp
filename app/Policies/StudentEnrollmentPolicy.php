<?php

namespace App\Policies;

use App\Models\StudentEnrollment;
use App\Models\User;

class StudentEnrollmentPolicy
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
        return $user->can('student_enrollments.view');
    }

    public function view(User $user, StudentEnrollment $studentEnrollment): bool
    {
        return $user->can('student_enrollments.view');
    }

    public function create(User $user): bool
    {
        return $user->can('student_enrollments.create');
    }

    public function update(User $user, StudentEnrollment $studentEnrollment): bool
    {
        return $user->can('student_enrollments.update');
    }

    public function delete(User $user, StudentEnrollment $studentEnrollment): bool
    {
        return $user->can('student_enrollments.delete');
    }

    public function restore(User $user, StudentEnrollment $studentEnrollment): bool
    {
        return false;
    }

    public function forceDelete(User $user, StudentEnrollment $studentEnrollment): bool
    {
        return false;
    }
}
