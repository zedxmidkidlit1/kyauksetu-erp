<?php

namespace App\Policies;

use App\Models\AcademicYear;
use App\Models\User;

class AcademicYearPolicy
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
        return $user->can('academic_years.view');
    }

    public function view(User $user, AcademicYear $academicYear): bool
    {
        return $user->can('academic_years.view');
    }

    public function create(User $user): bool
    {
        return $user->can('academic_years.create');
    }

    public function update(User $user, AcademicYear $academicYear): bool
    {
        return $user->can('academic_years.update');
    }

    public function delete(User $user, AcademicYear $academicYear): bool
    {
        return $user->can('academic_years.delete');
    }

    public function restore(User $user, AcademicYear $academicYear): bool
    {
        return false;
    }

    public function forceDelete(User $user, AcademicYear $academicYear): bool
    {
        return false;
    }
}
