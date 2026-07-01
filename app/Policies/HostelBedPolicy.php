<?php

namespace App\Policies;

use App\Models\HostelBed;
use App\Models\User;

class HostelBedPolicy
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
        return $user->can('hostel_beds.view');
    }

    public function view(User $user, HostelBed $hostelBed): bool
    {
        return $user->can('hostel_beds.view');
    }

    public function create(User $user): bool
    {
        return $user->can('hostel_beds.create');
    }

    public function update(User $user, HostelBed $hostelBed): bool
    {
        return $user->can('hostel_beds.update');
    }

    public function delete(User $user, HostelBed $hostelBed): bool
    {
        return $user->can('hostel_beds.delete');
    }

    public function restore(User $user, HostelBed $hostelBed): bool
    {
        return false;
    }

    public function forceDelete(User $user, HostelBed $hostelBed): bool
    {
        return false;
    }
}
