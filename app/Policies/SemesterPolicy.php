<?php

namespace App\Policies;

use App\Models\Semester;
use App\Models\User;

class SemesterPolicy
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
        return $user->can('semesters.view');
    }

    public function view(User $user, Semester $semester): bool
    {
        return $user->can('semesters.view');
    }

    public function create(User $user): bool
    {
        return $user->can('semesters.create');
    }

    public function update(User $user, Semester $semester): bool
    {
        return $user->can('semesters.update');
    }

    public function delete(User $user, Semester $semester): bool
    {
        return $user->can('semesters.delete');
    }

    public function restore(User $user, Semester $semester): bool
    {
        return false;
    }

    public function forceDelete(User $user, Semester $semester): bool
    {
        return false;
    }
}
