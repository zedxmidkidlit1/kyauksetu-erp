<?php

namespace App\Policies;

use App\Models\StaffPosition;
use App\Models\User;

class StaffPositionPolicy
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
        return $user->can('staff_positions.view');
    }

    public function view(User $user, StaffPosition $staffPosition): bool
    {
        return $user->can('staff_positions.view');
    }

    public function create(User $user): bool
    {
        return $user->can('staff_positions.create');
    }

    public function update(User $user, StaffPosition $staffPosition): bool
    {
        return $user->can('staff_positions.update');
    }

    public function delete(User $user, StaffPosition $staffPosition): bool
    {
        return $user->can('staff_positions.delete');
    }

    public function restore(User $user, StaffPosition $staffPosition): bool
    {
        return false;
    }

    public function forceDelete(User $user, StaffPosition $staffPosition): bool
    {
        return false;
    }
}
