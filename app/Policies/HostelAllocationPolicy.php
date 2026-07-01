<?php

namespace App\Policies;

use App\Models\HostelAllocation;
use App\Models\User;

class HostelAllocationPolicy
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
        return $user->can('hostel_allocations.view');
    }

    public function view(User $user, HostelAllocation $hostelAllocation): bool
    {
        return $user->can('hostel_allocations.view');
    }

    public function create(User $user): bool
    {
        return $user->can('hostel_allocations.create');
    }

    public function update(User $user, HostelAllocation $hostelAllocation): bool
    {
        return $user->can('hostel_allocations.update');
    }

    public function delete(User $user, HostelAllocation $hostelAllocation): bool
    {
        return $user->can('hostel_allocations.delete');
    }

    public function restore(User $user, HostelAllocation $hostelAllocation): bool
    {
        return false;
    }

    public function forceDelete(User $user, HostelAllocation $hostelAllocation): bool
    {
        return false;
    }
}
