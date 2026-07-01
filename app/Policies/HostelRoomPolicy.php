<?php

namespace App\Policies;

use App\Models\HostelRoom;
use App\Models\User;

class HostelRoomPolicy
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
        return $user->can('hostel_rooms.view');
    }

    public function view(User $user, HostelRoom $hostelRoom): bool
    {
        return $user->can('hostel_rooms.view');
    }

    public function create(User $user): bool
    {
        return $user->can('hostel_rooms.create');
    }

    public function update(User $user, HostelRoom $hostelRoom): bool
    {
        return $user->can('hostel_rooms.update');
    }

    public function delete(User $user, HostelRoom $hostelRoom): bool
    {
        return $user->can('hostel_rooms.delete');
    }

    public function restore(User $user, HostelRoom $hostelRoom): bool
    {
        return false;
    }

    public function forceDelete(User $user, HostelRoom $hostelRoom): bool
    {
        return false;
    }
}
