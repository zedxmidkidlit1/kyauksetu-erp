<?php

namespace App\Policies;

use App\Models\StudentProfile;
use App\Models\User;

class StudentProfilePolicy
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
        return $user->can('students.view');
    }

    public function view(User $user, StudentProfile $studentProfile): bool
    {
        return $user->can('students.view');
    }

    public function create(User $user): bool
    {
        return $user->can('students.create');
    }

    public function update(User $user, StudentProfile $studentProfile): bool
    {
        return $user->can('students.update');
    }

    public function delete(User $user, StudentProfile $studentProfile): bool
    {
        return false;
    }

    public function restore(User $user, StudentProfile $studentProfile): bool
    {
        return false;
    }

    public function forceDelete(User $user, StudentProfile $studentProfile): bool
    {
        return false;
    }
}
