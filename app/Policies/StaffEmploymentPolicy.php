<?php

namespace App\Policies;

use App\Models\StaffEmployment;
use App\Models\User;

class StaffEmploymentPolicy
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
        return $user->can('staff_employments.view');
    }

    public function view(User $user, StaffEmployment $staffEmployment): bool
    {
        return $user->can('staff_employments.view');
    }

    public function create(User $user): bool
    {
        return $user->can('staff_employments.create');
    }

    public function update(User $user, StaffEmployment $staffEmployment): bool
    {
        return $user->can('staff_employments.update');
    }

    public function delete(User $user, StaffEmployment $staffEmployment): bool
    {
        return $user->can('staff_employments.delete');
    }

    public function restore(User $user, StaffEmployment $staffEmployment): bool
    {
        return false;
    }

    public function forceDelete(User $user, StaffEmployment $staffEmployment): bool
    {
        return false;
    }
}
