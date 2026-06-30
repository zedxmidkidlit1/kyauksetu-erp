<?php

namespace App\Policies;

use App\Models\Major;
use App\Models\User;

class MajorPolicy
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
        return $user->can('majors.view');
    }

    public function view(User $user, Major $major): bool
    {
        return $user->can('majors.view');
    }

    public function create(User $user): bool
    {
        return $user->can('majors.create');
    }

    public function update(User $user, Major $major): bool
    {
        return $user->can('majors.update');
    }

    public function delete(User $user, Major $major): bool
    {
        return $user->can('majors.delete');
    }

    public function restore(User $user, Major $major): bool
    {
        return false;
    }

    public function forceDelete(User $user, Major $major): bool
    {
        return false;
    }
}
