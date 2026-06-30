<?php

namespace App\Policies;

use App\Models\TeachingAssignment;
use App\Models\User;

class TeachingAssignmentPolicy
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
        return $user->can('teaching_assignments.view');
    }

    public function view(User $user, TeachingAssignment $teachingAssignment): bool
    {
        return $user->can('teaching_assignments.view');
    }

    public function create(User $user): bool
    {
        return $user->can('teaching_assignments.create');
    }

    public function update(User $user, TeachingAssignment $teachingAssignment): bool
    {
        return $user->can('teaching_assignments.update');
    }

    public function delete(User $user, TeachingAssignment $teachingAssignment): bool
    {
        return $user->can('teaching_assignments.delete');
    }

    public function restore(User $user, TeachingAssignment $teachingAssignment): bool
    {
        return false;
    }

    public function forceDelete(User $user, TeachingAssignment $teachingAssignment): bool
    {
        return false;
    }
}
