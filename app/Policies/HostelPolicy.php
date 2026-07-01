<?php

namespace App\Policies;

use App\Models\Hostel;
use App\Models\User;

class HostelPolicy
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
        return $user->can('hostels.view');
    }

    public function view(User $user, Hostel $hostel): bool
    {
        return $user->can('hostels.view');
    }

    public function create(User $user): bool
    {
        return $user->can('hostels.create');
    }

    public function update(User $user, Hostel $hostel): bool
    {
        return $user->can('hostels.update');
    }

    public function delete(User $user, Hostel $hostel): bool
    {
        return $user->can('hostels.delete');
    }

    public function restore(User $user, Hostel $hostel): bool
    {
        return false;
    }

    public function forceDelete(User $user, Hostel $hostel): bool
    {
        return false;
    }
}
