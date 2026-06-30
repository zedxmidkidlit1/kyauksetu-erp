<?php

namespace App\Policies;

use App\Models\StaffProfile;
use App\Models\User;

class StaffProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function view(User $user, StaffProfile $staffProfile): bool
    {
        return $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, StaffProfile $staffProfile): bool
    {
        return $user->hasRole('super_admin');
    }

    public function delete(User $user, StaffProfile $staffProfile): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, StaffProfile $staffProfile): bool
    {
        return false;
    }

    public function forceDelete(User $user, StaffProfile $staffProfile): bool
    {
        return false;
    }
}
